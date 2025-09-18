<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property int $term
 * @property int|null $solution_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property Solution $solution
 */
class Request extends ActiveRecord
{
    const SOLUTION_APPROVED = 1;
    const SOLUTION_REJECTED = 2;

    public static function tableName()
    {
        return '{{%request}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'term', 'solution_id'], 'integer'],
            [['amount'], 'number', 'min' => 1],
            [['term'], 'integer', 'min' => 1],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['solution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Solution::class, 'targetAttribute' => ['solution_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'term' => 'Term',
            'solution_id' => 'Solution ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getSolution()
    {
        return $this->hasOne(Solution::class, ['id' => 'solution_id']);
    }

    public function isApproved()
    {
        return $this->solution_id === self::SOLUTION_APPROVED;
    }
}