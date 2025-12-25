<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\Cors;

class HistoryController extends ActiveController
{
    public $modelClass = 'app\models\History';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $behaviors;
    }
}
