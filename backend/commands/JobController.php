<?php

namespace app\commands;

use yii\console\Controller;
use app\jobs\CheckExpirationJob;
use Yii;

class JobController extends Controller
{
    public function actionStart()
    {
        Yii::$app->queue->push(new CheckExpirationJob());
        echo "Expiration Check Job Started.\n";
    }
}
