<?php

namespace common\models;

class Company extends \common\models\base\CompanyBase
{
    //to update created_at and updated_at fields.
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        } else {
            return false;
        }
    }
}