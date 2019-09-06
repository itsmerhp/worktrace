<?php

namespace common\models;

class Meals extends \common\models\base\MealsBase
{  
	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
			[['name','cuisine_id','image'], 'required']
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
	
	/**
	* @inheritdoc
	*/
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'cuisine_id' => 'Cuisine',
			'name' => 'Name',
			'image' => 'Image',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

}