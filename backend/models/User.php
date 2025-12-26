<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    const SHARED_PASSWORD = 'secret';


    public static function findIdentity($id)
    {
        return new static([
            'id' => $id,
            'username' => 'user',
            'password' => self::SHARED_PASSWORD,
            'authKey' => 'test100key',
            'accessToken' => "token-user",
        ]);
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (strpos($token, 'token-') === 0) {
            $username = substr($token, 6);
            return new static([
                'id' => 100,
                'username' => $username,
                'password' => self::SHARED_PASSWORD,
                'authKey' => "auth-key-$username",
                'accessToken' => $token,
            ]);
        }
        return null;
    }


    public static function findByUsername($username)
    {
        return new static([
            'id' => 100,
            'username' => $username,
            'password' => self::SHARED_PASSWORD,
            'authKey' => "auth-key-$username",
            'accessToken' => "token-$username",
        ]);
    }


    public function getId()
    {
        return $this->id;
    }


    public function getAuthKey()
    {
        return $this->authKey;
    }


    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $password === self::SHARED_PASSWORD;
    }
}
