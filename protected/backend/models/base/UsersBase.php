<?php

namespace backend\models\base;

use Yii;
use common\models\Dishes;
use common\models\Ratings;
use common\models\Restaurants;
use common\models\UsersAccessTokens;

/**
 * This is the model class for table "users".
*
    * @property integer $user_id
    * @property integer $role_id
    * @property integer $user_type
    * @property string $name
    * @property string $email
    * @property string $profile_pic
    * @property string $password
    * @property integer $is_otp_verified
    * @property integer $status
    * @property string $password_reset_token
    * @property string $latitude
    * @property string $longitude
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Dishes[] $dishes
            * @property Ratings[] $ratings
            * @property Restaurants[] $restaurants
            * @property UsersAccessTokens[] $usersAccessTokens
    */
class UsersBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'users';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['role_id', 'user_type', 'is_otp_verified', 'status'], 'integer'],
            [['email', 'password'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'profile_pic', 'password', 'password_reset_token'], 'string', 'max' => 255],
            [['latitude', 'longitude'], 'string', 'max' => 45],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'user_id' => 'User ID',
    'role_id' => 'Role ID',
    'user_type' => 'User Type',
    'name' => 'Name',
    'email' => 'Email',
    'profile_pic' => 'Profile Pic',
    'password' => 'Password',
    'is_otp_verified' => 'Is Otp Verified',
    'status' => 'Status',
    'password_reset_token' => 'Password Reset Token',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDishes()
    {
    return $this->hasMany(Dishes::className(), ['user_id' => 'user_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRatings()
    {
    return $this->hasMany(Ratings::className(), ['user_id' => 'user_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRestaurants()
    {
    return $this->hasMany(Restaurants::className(), ['user_id' => 'user_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUsersAccessTokens()
    {
    return $this->hasMany(UsersAccessTokens::className(), ['user_id' => 'user_id']);
    }
}