<?php

namespace common\models\base;

use Yii;
use common\models\Users;

/**
 * This is the model class for table "restaurants".
*
    * @property integer $id
    * @property integer $user_id
    * @property string $name
    * @property string $mobile
    * @property string $address
    * @property string $restaurant_lat
    * @property string $restaurant_long
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Users $user
    */
class RestaurantsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'restaurants';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'mobile'], 'string', 'max' => 255],
            [['restaurant_lat', 'restaurant_long'], 'string', 'max' => 45],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'user_id' => 'User ID',
    'name' => 'Name',
    'mobile' => 'Mobile',
    'address' => 'Address',
    'restaurant_lat' => 'Restaurant Lat',
    'restaurant_long' => 'Restaurant Long',
    'status' => 'Status',
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