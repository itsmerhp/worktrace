<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "restaurant_otp".
*
    * @property integer $id
    * @property string $email
    * @property string $restaurant_otp
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
*/
class RestaurantOtpBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'restaurant_otp';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['email'], 'required'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['email', 'restaurant_otp'], 'string', 'max' => 255],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'email' => 'Email',
    'restaurant_otp' => 'Restaurant Otp',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}
}