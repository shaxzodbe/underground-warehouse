<?php

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
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Logic: If Cooling AND outside Fridge (3x3) -> Set Expiration
        // Fridge is (0,0) to (2,2) -> x < 3 && y < 3
        if ($this->type === self::TYPE_COOLING && $this->status !== self::STATUS_EXPIRED) {
            $inFridge = ($this->x < 3 && $this->y < 3);

            if ($inFridge) {
                $this->expires_at = null;
            } else {
                if (empty($this->expires_at)) {
                    $this->expires_at = date('Y-m-d H:i:s', time() + 30);
                }
            }
        }

        return true;
    }
}
