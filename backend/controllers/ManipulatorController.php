<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\services\ManipulatorService;
use yii\filters\Cors;

class ManipulatorController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $service = new ManipulatorService();
        return $service->getState();
    }

    public function actionExecute()
    {
        $commands = Yii::$app->request->post('command');
        if (!$commands) {
            return ['error' => 'Command is required'];
        }

        $service = new ManipulatorService();
        $service->executeCommands($commands);

        return [
            'status' => 'success',
            'state' => $service->getState(),
            'compressed' => $service->compress($commands)
        ];
    }
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }
}
