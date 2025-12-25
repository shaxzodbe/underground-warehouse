<?php

namespace app\jobs;

use app\models\Sample;
use app\models\History;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class CheckExpirationJob extends BaseObject implements JobInterface
{


    public function execute($queue)
    {
        // Find all samples that are past their expiration time AND are not yet marked expired
        // Condition: expires_at < NOW AND status != 'expired' AND type='cooling'
        $expiredSamples = Sample::find()
            ->where(['type' => Sample::TYPE_COOLING])
            ->andWhere(['not', ['expires_at' => null]])
            ->andWhere(['<', 'expires_at', date('Y-m-d H:i:s')])
            ->andWhere(['!=', 'status', Sample::STATUS_EXPIRED])
            ->all();

        foreach ($expiredSamples as $sample) {
            $sample->status = Sample::STATUS_EXPIRED;
            if ($sample->save()) {
                History::log('expire', ['sample_id' => $sample->id]);
            }
        }

        // Re-schedule self to run again in 5 seconds (Simple loop)
        Yii::$app->queue->delay(5)->push(new self());
    }
}
