<?php

namespace common\models\base;

use Yii;
use common\models\DishMedia;
use common\models\Cuisines;
use common\models\Meals;
use common\models\QuickFoods;
use common\models\Users;

/**
 * This is the model class for table "dishes".
*
    * @property integer $id
    * @property integer $user_id
    * @property integer $cuisine_id
    * @property integer $meal_id
    * @property integer $quick_food_id
    * @property string $title
    * @property string $description
    * @property double $price
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property DishMedia[] $dishMedia
            * @property Cuisines $cuisine
            * @property Meals $meal
            * @property QuickFoods $quickFood
            * @property Users $user
    */
class DishesBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'dishes';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['user_id', 'cuisine_id', 'meal_id', 'quick_food_id', 'status'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['cuisine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cuisines::className(), 'targetAttribute' => ['cuisine_id' => 'id']],
            [['meal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meals::className(), 'targetAttribute' => ['meal_id' => 'id']],
            [['quick_food_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuickFoods::className(), 'targetAttribute' => ['quick_food_id' => 'id']],
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
    'cuisine_id' => 'Cuisine ID',
    'meal_id' => 'Meal ID',
    'quick_food_id' => 'Quick Food ID',
    'title' => 'Title',
    'description' => 'Description',
    'price' => 'Price',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDishMedia()
    {
    return $this->hasMany(DishMedia::className(), ['dish_id' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCuisine()
    {
    return $this->hasOne(Cuisines::className(), ['id' => 'cuisine_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getMeal()
    {
    return $this->hasOne(Meals::className(), ['id' => 'meal_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getQuickFood()
    {
    return $this->hasOne(QuickFoods::className(), ['id' => 'quick_food_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUser()
    {
    return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}