<?php

namespace common\models;

class UsersAccessTokens extends \common\models\base\UsersAccessTokensBase
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
}