<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "countries".
*
    * @property integer $id
    * @property string $code
    * @property string $name
    * @property integer $dial_code
    * @property string $currency_name
    * @property string $currency_symbol
    * @property string $currency_code
*/
class CountriesBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'countries';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['code', 'name', 'dial_code', 'currency_name', 'currency_symbol', 'currency_code'], 'required'],
            [['dial_code'], 'integer'],
            [['code'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 150],
            [['currency_name', 'currency_symbol', 'currency_code'], 'string', 'max' => 20],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'code' => 'Code',
    'name' => 'Name',
    'dial_code' => 'Dial Code',
    'currency_name' => 'Currency Name',
    'currency_symbol' => 'Currency Symbol',
    'currency_code' => 'Currency Code',
];
}
}