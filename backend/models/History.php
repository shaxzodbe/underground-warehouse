<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "history".
 *
 * @property int $id
 * @property string $action
 * @property string|null $details
 * @property string $created_at
 */
class History extends ActiveRecord
{
    public static function tableName()
    {
        return 'history';
    }

    public function rules()
    {
        return [
            [['action'], 'required'],
            [['details', 'created_at'], 'safe'],
            [['action'], 'string', 'max' => 255],
        ];
    }

    public static function log($action, $details = null)
    {
        $history = new self();
        $history->action = $action;
        $history->details = is_array($details) ? json_encode($details) : $details;
        return $history->save();
    }
}
