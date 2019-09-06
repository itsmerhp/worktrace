<?php

namespace common\models\base;

use Yii;
use common\models\Meals;

/**
 * This is the model class for table "cuisines".
*
    * @property integer $id
    * @property string $name
    * @property string $image
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Meals[] $meals
    */
class CuisinesBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'cuisines';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['image'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'name' => 'Name',
    'image' => 'Image',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getMeals()
    {
    return $this->hasMany(Meals::className(), ['cuisine_id' => 'id']);
    }
}