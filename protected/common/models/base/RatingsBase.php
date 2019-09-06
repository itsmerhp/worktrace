<?php

namespace common\models\base;

use Yii;
use common\models\Dishes;
use common\models\Users;

/**
 * This is the model class for table "ratings".
*
    * @property integer $id
    * @property integer $dish_id
    * @property integer $user_id
    * @property integer $dish_rating
    * @property integer $quality_rating
    * @property integer $appearance_rating
    * @property string $feedback
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Dishes $dish
            * @property Users $user
    */
class RatingsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'ratings';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['dish_id', 'user_id', 'dish_rating', 'quality_rating', 'appearance_rating', 'status'], 'integer'],
            [['feedback'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['dish_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dishes::className(), 'targetAttribute' => ['dish_id' => 'id']],
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
    'dish_id' => 'Dish ID',
    'user_id' => 'User ID',
    'dish_rating' => 'Dish Rating',
    'quality_rating' => 'Quality Rating',
    'appearance_rating' => 'Appearance Rating',
    'feedback' => 'Feedback',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDish()
    {
    return $this->hasOne(Dishes::className(), ['id' => 'dish_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUser()
    {
    return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}