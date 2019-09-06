<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * ForgotPasswordForm form
 */
class ForgotPasswordForm extends Model {

    public $email;
    
   /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['email'], 'required'],
                ['email', 'email'],
                ['email','validateEmail']
            ];
    }

    public function validateEmail(){
        $ASvalidateemail = Users::find()->where("email = '".$this->email."' && role_id =1")->all();        
        if(empty($ASvalidateemail)){
             $this->addError('email', 'Incorrect Email Address.');
             return true;
        }

    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
           'email' => Yii::t('app', 'Email'),
        );
    } 

    
}
