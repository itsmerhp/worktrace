<?php

namespace common\models;

use Yii;
use yii\base\Model;


/**
 * ChangePasswordForm form
 */
class ChangePasswordForm extends Model {

    public $currentPassword;
    public $newPassword;
    public $retypePassword;
    
   /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['currentPassword','newPassword','retypePassword'], 'required'],
            ['currentPassword','validatepassword'],
            ['newPassword','validateNewPassword'],
            ['retypePassword', 'compare','compareAttribute'=>'newPassword','message'=>'Repeat New Password and New Password must be same.'],
            [['currentPassword','newPassword','retypePassword'], 'safe']
            //['newPassword', 'compare','compareAttribute'=>'retypePassword'],
            
        ];
    }

   

    public function validatepassword(){
        $ASvalidatemodel = Users::findOne(Yii::$app->user->id);
        if(!password_verify($this->currentPassword, $ASvalidatemodel->password)){
             $this->addError('currentPassword', 'Please enter correct Password');
             return true;
       }

    }

      public function validateNewPassword(){
        $ASvalidatemodel = Users::findOne(Yii::$app->user->id);        
        if(password_verify($this->newPassword, $ASvalidatemodel->password)){
             $this->addError('newPassword', 'This password has already been taken');
             return true;
       }

    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
           'currentPassword' => Yii::t('app', 'Current  Password'),
           'newPassword' => Yii::t('app', 'New Password'),
           'retypePassword' => Yii::t('app', 'Repeat New Password'),
        );
    } 

}
