<?php

namespace backend\models;
use common\models\Restaurants;

class Users extends \backend\models\base\UsersBase
{
    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRestaurantDetails()
    {
    return $this->hasOne(Restaurants::className(), ['user_id' => 'user_id']);
    }
}