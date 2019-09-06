<?php
namespace common\models;

use yii\base\Model;
use Yii;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $rememberMe = true;

    /**
     * @var \common\models\User
     */
    private $_user = false;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['email', 'email'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
            [['email', 'password'], 'required'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute The attribute currently being validated.
     * @param array  $params    The additional name-value pairs.
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) 
        {
            $user = $this->getUser();
            
            if (!$user || !password_verify($this->password,$user->password)) {
                $this->addError($attribute, Yii::t('app','Incorrect Email or Password.'));
            }else if (!$user || $user->status != Users::STATUS_ACTIVE) {
                $this->addError($attribute, Yii::t('app','Your Acount has been in active so please contact to administrator.'));
            }else if (!$user || $user->role_id != Users::ADMIN_ROLE) {
                $this->addError($attribute, Yii::t('app','Only Admin can login from Admin Panel.'));
            }
        }
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    /**
     * Logs in a user using the provided username|email and password.
     *
     * @return bool Whether the user is logged in successfully.
     */
    public function login()
    {
        if ($this->validate()) 
        {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; 
            if($this->rememberMe == 1){ 
                setcookie (\Yii::$app->params['siteName']."_admin_email", $this->email, time()+3600*24*4);
                setcookie (\Yii::$app->params['siteName']."_admin_password", $this->password, time()+3600*24*4);
            }else{
                setcookie (\Yii::$app->params['siteName']."_admin_email", '');
                setcookie (\Yii::$app->params['siteName']."_admin_password", '');
            }
           return Yii::$app->user->login($this->getUser(), $duration);
        } 
        else 
        {
            return false;
        }  
    }

    /**
     * Finds user by username or email in 'lwe' scenario.
     *
     * @return User|null|static
     */
    public function getUser()
    {
        if ($this->_user === false) 
        {
            $this->_user = Users::findByEmail($this->email);
        }

        return $this->_user;
    }
}
