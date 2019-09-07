<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company".
*
    * @property integer $id
    * @property string $name
    * @property string $email
    * @property string $mobile
    * @property string $address
    * @property string $company_logo
    * @property string $latitude
    * @property string $longitude
    * @property integer $status
    * @property integer $created_at
    * @property integer $updated_at
*/
class CompanyBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'company';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['address', 'company_logo'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'email', 'mobile', 'latitude', 'longitude'], 'string', 'max' => 255],
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
    'email' => 'Email',
    'mobile' => 'Mobile',
    'address' => 'Address',
    'company_logo' => 'Company Logo',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}
}