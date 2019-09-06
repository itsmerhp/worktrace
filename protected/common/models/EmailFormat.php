<?php

namespace common\models;

class EmailFormat extends \common\models\base\EmailFormatBase
{
    //to update created_at and updated_at fields.
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
}