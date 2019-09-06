<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "pages".
*
    * @property integer $id
    * @property string $page_name
    * @property string $page_content
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
*/
class PagesBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'pages';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['page_content'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['page_name'], 'string', 'max' => 255],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'page_name' => 'Page Name',
    'page_content' => 'Page Content',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}
}