<?php

namespace common\models\base;

use Yii;
use common\models\Dishes;

/**
 * This is the model class for table "dish_media".
*
    * @property integer $id
    * @property integer $dish_id
    * @property integer $type
    * @property string $url
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Dishes $dish
    */
class DishMediaBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'dish_media';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['dish_id', 'type', 'status'], 'integer'],
            [['url'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['dish_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dishes::className(), 'targetAttribute' => ['dish_id' => 'id']],
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
    'type' => 'Type',
    'url' => 'Url',
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
}