<?php

namespace common\models\base;

use Yii;
use common\models\Cuisines;

/**
 * This is the model class for table "meals".
*
    * @property integer $id
    * @property integer $cuisine_id
    * @property string $name
    * @property string $image
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
    *
            * @property Cuisines $cuisine
    */
class MealsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'meals';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['cuisine_id', 'status'], 'integer'],
            [['image'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['cuisine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cuisines::className(), 'targetAttribute' => ['cuisine_id' => 'id']],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'cuisine_id' => 'Cuisine ID',
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
    public function getCuisine()
    {
    return $this->hasOne(Cuisines::className(), ['id' => 'cuisine_id']);
    }
}