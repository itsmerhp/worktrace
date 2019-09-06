<?php

namespace common\models;
use common\models\Countries;

class Restaurants extends \common\models\base\RestaurantsBase
{
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
    * @return \yii\db\ActiveQuery
    */
    public function getCountry()
    {
    return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }
}