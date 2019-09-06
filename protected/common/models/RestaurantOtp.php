<?php

namespace common\models;
use common\models\Users;
use Yii;

class RestaurantOtp extends \common\models\base\RestaurantOtpBase
{    
	/**
	* @inheritdoc
	*/
	public function rules()
	{
			return [
				[['email'], 'required'],
				[['email'], 'email'],
				[['email'], 'isRegistered'],
				[['status'], 'integer'],
				[['created_at', 'updated_at'], 'safe'],
				[['email', 'restaurant_otp'], 'string', 'max' => 255],
			];
	}
	
	//Restaurant unique check
	public function isRegistered($attribute){
		//set restaurant_id
		$email = !empty($this->email)?$this->email:'';
		//Check if email exist or not in Restaurant OTP table
		$restaurantOTPDetails = RestaurantOtp::find()->where(['email'=>$email])->one();
				
		if(!empty($restaurantOTPDetails)){
			$this->addError($attribute,'OTP has already generated for '.$this->email);
		}	
		
		//Check if email is already registered with restaurant
		$restaurantUserDetails = Users::find()->where(['email'=>$email,'role_id'=>  Yii::$app->params['USER_ROLES']['app_user'],'user_type'=>  Yii::$app->params['USER_TYPE']['restaurant']])->one();
				
		if(!empty($restaurantUserDetails)){
			$this->addError($attribute,$this->email." is already registered.");
		}	
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
			'email' => 'Email',
			'restaurant_otp' => 'Restaurant OTP',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}
}