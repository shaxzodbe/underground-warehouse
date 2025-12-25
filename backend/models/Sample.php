<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Sample extends ActiveRecord
{
    const TYPE_NORMAL = 'normal';
    const TYPE_COOLING = 'cooling';

    const STATUS_STORED = 'stored';
    const STATUS_HELD = 'held';
    const STATUS_DROPPED = 'dropped';
    const STATUS_EXPIRED = 'expired';

    public static function tableName()
    {
        return 'samples';
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'status'], 'string'],
            [['created_at', 'expires_at'], 'safe'],
            [['x', 'y'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['type', 'in', 'range' => [self::TYPE_NORMAL, self::TYPE_COOLING]],
            ['status', 'in', 'range' => [self::STATUS_STORED, self::STATUS_HELD, self::STATUS_DROPPED, self::STATUS_EXPIRED]],
        ];
    }
}
