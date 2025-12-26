<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        if (!$username || !$password) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Username and password are required'];
        }

        $user = \app\models\User::findByUsername($username);
        if ($user && $user->validatePassword($password)) {
            return [
                'token' => $user->accessToken,
                'username' => $user->username,
            ];
        }

        Yii::$app->response->statusCode = 401;
        return ['error' => 'Incorrect username or password'];
    }
}
