<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "email_format".
*
    * @property integer $id
    * @property string $title
    * @property string $subject
    * @property string $body
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
*/
class EmailFormatBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'email_format';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['title', 'subject', 'body', 'created_at', 'status'], 'required'],
            [['body'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'subject'], 'string', 'max' => 255],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'title' => 'Title',
    'subject' => 'Subject',
    'body' => 'Body',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}
}