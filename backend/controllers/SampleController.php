<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\Cors;

class SampleController extends ActiveController
{
    public $modelClass = 'app\models\Sample';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $behaviors;
    }
}
