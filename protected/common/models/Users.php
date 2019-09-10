<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use yii\base\Security;
use yii\web\IdentityInterface;
use api\components\CommonApiHelper;
use common\models\Company;

class Users extends \yii\db\ActiveRecord implements IdentityInterface {

    /**
     * @inheritdoc
     */
    public $auth_key;

    const STATUS_ACTIVE = 1;
    const ADMIN_ROLE = 1;

    public static function tableName() {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['role_id', 'company_id', 'reporting_to', 'created_by', 'updated_by', 'status'], 'integer'],
            [['email', 'name', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['email', 'name', 'address', 'mobile', 'profile_pic', 'password', 'password_reset_token', 'latitude', 'longitude'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_id' => 'User ID',
            'role_id' => 'Role',
            'company_id' => 'Company',
            'reporting_to' => 'Reporting To',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'name' => 'Name',
            'address' => 'Address',
            'profile_pic' => 'Profile Pic',
            'password' => 'Password',
            'status' => 'Status',
            'password_reset_token' => 'Password Reset Token',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function () {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Sends email to registered user with reset password link.
     *
     * @param  object $user Registered user.
     * @return bool         Whether the message has been sent successfully.
     */
    public function sendPasswordResetEmail($user) {
        if (!empty($user->email)) {
            $emailformatemodel = EmailFormat::findOne(["id" => Yii::$app->params['EMAIL_TEMPLATE_ID']['forgot_password'], "status" => '1']);
            if ($emailformatemodel) {
                $resetLink = Url::to('@siteRoot/site/reset-app-password?token=' . $user->password_reset_token, true);

                //create email body
                $AreplaceString = array('{name}' => $user->name, '{email}' => $user->email, '{link}' => $resetLink);
                $body = CommonApiHelper::MailTemplate($AreplaceString, $emailformatemodel->body);
                $ssSubject = $emailformatemodel->subject;
                //send email to new registered user
                Yii::$app->mailer->compose()
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                        ->setTo($user->email)
                        ->setSubject($ssSubject)
                        ->setHtmlBody($body)
                        ->send();
            }
        }
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email]);
    }

    public function beforeSave($insert) {
        //$this->birth_date = date("Y-m-d", strtotime($this->birth_date));
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

}
