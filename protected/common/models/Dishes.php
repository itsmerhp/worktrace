<?php

namespace common\models;
use backend\models\Users;

class Dishes extends \common\models\base\DishesBase
{
	public $user_name,$cuisine_name,$meal_name,$quick_food_name,$total_ratings,$avg_dish_rating,$avg_quality_rating,$avg_appearance_rating,$dish_images,$dish_video,$dish_video_duration;
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                    $this->created_at = date("Y-m-d H:i:s");
            }
            $this->updated_at = date("Y-m-d H:i:s");
            return true;
        } else {
            return false;
        }
    }
	/**
	* @inheritdoc
	*/
	public function rules()
	{
			return [
				[['user_id', 'cuisine_id', 'meal_id', 'quick_food_id', 'status'], 'integer'],
				[['user_id', 'cuisine_id', 'meal_id', 'title', 'price'], 'required'],
				[['description'], 'string'],
				[['price'], 'number'],
				[['dish_images'], 'file', 'skipOnEmpty' => false, 'extensions'=>['jpg', 'png', 'jpeg'], 'checkExtensionByMimeType'=>false, 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 3, 'on' => 'create'],
				[['dish_images'], 'file', 'extensions'=>['jpg', 'png', 'jpeg'], 'checkExtensionByMimeType'=>false, 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 3],
				[['dish_video'], 'file', 'skipOnEmpty' => true, 'extensions'=>['mp4'], 'checkExtensionByMimeType'=>false, 'maxSize' => 1024 * 1024 * 15],
				[['created_at', 'updated_at','dish_images','dish_video','dish_video_duration'], 'safe'],
				[['title'], 'string', 'max' => 255],
				[['cuisine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cuisines::className(), 'targetAttribute' => ['cuisine_id' => 'id']],
				[['meal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meals::className(), 'targetAttribute' => ['meal_id' => 'id']],
				[['quick_food_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuickFoods::className(), 'targetAttribute' => ['quick_food_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
			];
	}
	/**
    * @return \yii\db\ActiveQuery
    */
    public function getUserRating()
    {
    return $this->hasOne(Ratings::className(), ['dish_id' => 'id']);
    }
	
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUser()
    {
    return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
	
	/**
    * @return \yii\db\ActiveQuery
    */
    public function getDishPrimaryImage()
    {
    return $this->hasOne(DishMedia::className(), ['dish_id' => 'id'])->orderBy(['dish_media.id'=>SORT_ASC]);
    }
	
	/**
	* @inheritdoc
	*/
	public function attributeLabels()
	{
	return [
		'id' => 'ID',
		'user_id' => 'Restaurant',
		'user_name' => 'User',
		'cuisine_name' => 'Cuisine',
		'meal_name' => 'Meal',
		'quick_food_name' => 'Quick Food',
		'cuisine_id' => 'Cuisine',
		'meal_id' => 'Meal',
		'quick_food_id' => 'Quick Food',
		'title' => 'Title',
		'description' => 'Description',
		'avg_dish_rating' => 'Dish',
		'avg_quality_rating' => 'Quality',
		'avg_appearance_rating' => 'Appearance',
		'price' => 'Price',
		'status' => 'Status',
		'created_at' => 'Created At',
		'updated_at' => 'Updated At',
	];
	}
}