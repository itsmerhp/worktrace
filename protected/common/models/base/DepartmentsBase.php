<?php

namespace common\models\base;

use Yii;
use common\models\Company;

/**
 * This is the model class for table "departments".
*
    * @property integer $id
    * @property integer $company_id
    * @property string $name
    * @property integer $status
    * @property integer $created_at
    * @property integer $updated_at
    *
            * @property Company $company
    */
class DepartmentsBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'departments';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['company_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'company_id' => 'Company ID',
    'name' => 'Name',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCompany()
    {
    return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
}