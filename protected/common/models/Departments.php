<?php

namespace common\models;

class Departments extends \common\models\base\DepartmentsBase
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