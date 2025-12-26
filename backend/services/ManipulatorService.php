<?php

namespace app\services;

use app\models\History;
use app\models\Sample;
use app\jobs\HistoryJob;
use Yii;
use yii\base\Component;
use yii\web\BadRequestHttpException;

class ManipulatorService extends Component
{
    private $redis;

    const MAX_X = 10;
    const MAX_Y = 10;
    const FRIDGE_W = 3;
    const FRIDGE_H = 3;

    const SAMPLE_LIFETIME = 30;

    public function init()
    {
        parent::init();
        $this->redis = Yii::$app->redis;
    }

    private function inFridge($x, $y)
    {
        return $x < self::FRIDGE_W && $y < self::FRIDGE_H;
    }

    private function updateExpiration(Sample $sample, $isInFridge)
    {
        if ($sample->type !== Sample::TYPE_COOLING)
            return;

        if ($isInFridge) {
            $sample->expires_at = null;
        } else {
            if (!$sample->expires_at) {
                $sample->expires_at = date('Y-m-d H:i:s', time() + self::SAMPLE_LIFETIME);
            }
        }
        $sample->save();
    }

    public function getState()
    {
        return [
            'x' => (int) ($this->redis->get('manipulator:x') ?? 0),
            'y' => (int) ($this->redis->get('manipulator:y') ?? 0),
            'holding' => $this->redis->get('manipulator:holding'),
        ];
    }

    public function executeCommands(string $commands)
    {
        preg_match_all('/(\d*)([^\d])/u', $commands, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $multiplierStr = $match[1];
            $char = $match[2];
            $count = ($multiplierStr === '') ? 1 : (int) $multiplierStr;

            for ($k = 0; $k < $count; $k++) {
                switch (mb_strtoupper($char)) {
                    case 'В':
                    case 'U':
                        $this->move(0, 1);
                        break;
                    case 'Н':
                    case 'D':
                        $this->move(0, -1);
                        break;
                    case 'Л':
                    case 'L':
                        $this->move(-1, 0);
                        break;
                    case 'П':
                    case 'R':
                        $this->move(1, 0);
                        break;
                    case 'О':
                    case 'P':
                        $this->pick();
                        break;
                    case 'Б':
                    case 'B':
                    case 'E':
                        $this->drop();
                        break;
                }
            }
        }
    }

    private function move($dx, $dy)
    {
        $state = $this->getState();
        $newX = $state['x'] + $dx;
        $newY = $state['y'] + $dy;

        // Boundary check
        if ($newX < 0 || $newX >= self::MAX_X || $newY < 0 || $newY >= self::MAX_Y) {
            return;
        }

        // Check Fridge Transition
        $wasFridge = $this->inFridge($state['x'], $state['y']);
        $newFridge = $this->inFridge($newX, $newY);

        if ($state['holding']) {
            if ($wasFridge !== $newFridge) {
                $sample = Sample::findOne($state['holding']);
                if ($sample) {
                    $this->updateExpiration($sample, $newFridge);
                } else {
                    $this->redis->del('manipulator:holding');
                }
            }
        }

        $this->redis->set('manipulator:x', $newX);
        $this->redis->set('manipulator:y', $newY);

        // If holding sample, update its coordinates? 
        // Samples on field have x,y. Held sample implies it moves with manipulator.
        // We can update sample coordinates on DROP.
        // Or update them continuously? 
        // For visualization simplicity, if status='held', frontend draws it at manipulator pos.

        $this->dispatchHistory('move', ['x' => $newX, 'y' => $newY]);
    }

    private function pick()
    {
        $state = $this->getState();
        if ($state['holding']) {
            return;
        }

        $sample = Sample::findOne(['x' => $state['x'], 'y' => $state['y'], 'status' => Sample::STATUS_STORED]);
        if ($sample) {
            $sample->status = Sample::STATUS_HELD;
            $sample->save();
            $this->redis->set('manipulator:holding', $sample->id);
            $this->dispatchHistory('pick', ['sample_id' => $sample->id]);

            $this->updateExpiration($sample, $this->inFridge($state['x'], $state['y']));
        }
    }

    private function drop()
    {
        $state = $this->getState();
        if (!$state['holding']) {
            return;
        }

        $sample = Sample::findOne($state['holding']);

        if (!$sample) {
            $this->redis->del('manipulator:holding');
            return;
        }

        // Check if cell is occupied (except by self logic, but holding implies it's not on grid)
        if ($sample->status !== Sample::STATUS_DROPPED) {
            $occupied = Sample::find()
                ->where(['x' => $state['x'], 'y' => $state['y'], 'status' => Sample::STATUS_STORED])
                ->exists();

            if ($occupied) {
                return;
            }
        }

        if ($sample) {
            $sample->status = Sample::STATUS_STORED;
            $sample->x = $state['x'];
            $sample->y = $state['y'];

            if ($sample->status !== Sample::STATUS_DROPPED) {
                $this->updateExpiration($sample, $this->inFridge($state['x'], $state['y']));
            }

            if ($state['x'] === self::MAX_X - 1 && $state['y'] === self::MAX_Y - 1) {
                $sample->status = Sample::STATUS_DROPPED;
                $sample->delete();
                $this->dispatchHistory('destroy', ['sample_id' => $sample->id]);
            } else {
                $sample->save();
                $this->dispatchHistory('drop', ['sample_id' => $sample->id]);
            }
        }

        $this->redis->del('manipulator:holding');
    }

    private function dispatchHistory($action, $details)
    {
        Yii::$app->queue->push(new HistoryJob([
            'action' => $action,
            'details' => $details
        ]));
    }

    public function compress(string $commands)
    {
        if (empty($commands))
            return '';

        $len = mb_strlen($commands);
        if ($len <= 4)
            return $commands;


        $best = $commands;

        for ($subLen = 1; $subLen <= $len / 2; $subLen++) {
            $sub = mb_substr($commands, 0, $subLen);

            $count = 1;
            $pos = $subLen;
            while ($pos + $subLen <= $len && mb_substr($commands, $pos, $subLen) === $sub) {
                $count++;
                $pos += $subLen;
            }

            if ($count > 1) {
                $prefix = ($subLen == 1) ? $count . $sub : $count . '(' . $this->compress($sub) . ')';
                $suffix = $this->compress(mb_substr($commands, $pos));

                $candidate = $prefix . $suffix;
                if (mb_strlen($candidate) < mb_strlen($best)) {
                    $best = $candidate;
                }
            }
        }

        return $best;
    }
}
