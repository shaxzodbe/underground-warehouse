<?php

namespace app\jobs;

use app\models\History;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class HistoryJob extends BaseObject implements JobInterface
{
    public $action;
    public $details;

    public function execute($queue)
    {
        History::log($this->action, $this->details);
    }
}
