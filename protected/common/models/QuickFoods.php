<?php

namespace common\models;

class QuickFoods extends \common\models\base\QuickFoodsBase
{    
	/**
	* @inheritdoc
	*/
	public function rules()
	{
			return [
				[['name','image'], 'required']
			];
	}
	
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