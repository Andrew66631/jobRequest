<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $description
 */
class Solution extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%solution}}';
    }

    public function rules()
    {
        return [
            [['description'], 'required'],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'идентификатор заявки',
            'description' => 'Решение',
        ];
    }
}