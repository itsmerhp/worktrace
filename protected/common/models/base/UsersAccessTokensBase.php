<?php

namespace common\models\base;

use Yii;
use common\models\Users;

/**
 * This is the model class for table "users_access_tokens".
*
    * @property integer $access_token_id
    * @property integer $user_id
    * @property string $device_token
    * @property integer $device_type
    * @property string $access_token
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Users $user
    */
class UsersAccessTokensBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'users_access_tokens';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['user_id', 'device_type'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['device_token', 'access_token'], 'string', 'max' => 255]
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'access_token_id' => 'Access Token ID',
    'user_id' => 'User ID',
    'device_token' => 'Device Token',
    'device_type' => 'Device Type',
    'access_token' => 'Access Token',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUser()
    {
    return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}