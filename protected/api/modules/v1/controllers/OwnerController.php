<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use common\models\Users;
use common\models\Company;
use yii\helpers\Url;
use common\components\Common;
use api\components\CommonApiHelper;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\models\EmailFormat;
use common\models\UsersAccessTokens;

/**
 * Description of OwnerController
 *
 * @author kittu
 */
class OwnerController extends Controller {

    // if you are doing non-verified stuff must have this set to false
    // so yii doesn't look for the token.

    public $enableCsrfValidation = false;

    public function init() {
        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);

        if (isset($post['user_id'])) {
            $accessToken = Yii::$app->request->headers->get('access_token');
            CommonApiHelper::checkUserStatus($post['user_id'], $accessToken);
        }

        parent::init();

        Yii::$app->user->enableSession = false;    // no sessions for this controller
        Yii::$app->user->loginUrl = null;     // no default login needed
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;    // default this controller to JSON, otherwise it's FORMAT_HTML
    }

    /**
     * this function used by the application user for email unique check.
     */
    public function actionUniqueEmailCheck() {
        //validate webservice
        $requiredParams = ['email'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];

        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);
        $email = $post['email'];

        //Email uniqueness check
        $userEmailCheck = Users::find()->where(['email' => $email])->asArray()->one();
        if (empty($userEmailCheck)) {
            return CommonApiHelper::return_success_response("Email is available");
        } else {
            return CommonApiHelper::return_error_response('Email has been already registered. Please try with different email Id.','2');
        }
    }

    /**
     * this function used by the application user for signUp purpose.
     */
    public function actionSignUp() {
        //validate webservice
        $requiredParams = ['company_name','owner_name', 'email'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];

        //try {
            $transaction = Yii::$app->db->beginTransaction();
            //Get request parameters.
            $post = Yii::$app->request->bodyParams;
            $post = array_map('trim', $post);

            $company_name = $post['company_name'];
            $owner_name = $post['owner_name'];
            $email = $post['email'];
            $mobile = !empty($post['mobile']) ? $post['mobile']: NULL;
            $address = !empty($post['address']) ? $post['address']: NULL;
            $latitude = !empty($post['latitude']) ? $post['latitude']: NULL;
            $longitude = !empty($post['longitude']) ? $post['longitude']: NULL;

            if (isset($email) && !empty($email)) {
                $emailExist = Users::findOne(['email' => $email]);
                if (isset($emailExist) && !empty($emailExist)) {
                    return CommonApiHelper::return_error_response("This email is already registered, Please try with different email", "2");
                }
            }
            
            //save company
            $company = new Company();
            $company->email = $email;
            $company->mobile = $mobile;
            $company->name = $company_name;
            $company->address = $address;
            $company->latitude = $latitude;
            $company->longitude = $longitude;
            
            //save the company logo.
            if (isset($_FILES['company_logo']['name']) && !empty($_FILES['company_logo']['name']))
            {
                $company->company_logo = time().$_FILES['company_logo']['name'];
                move_uploaded_file($_FILES['company_logo']['tmp_name'],Yii::$app->params['DOCUMENT_ROOT'].'uploads/company_logo/'.$company->company_logo);
            }
            else
            {
                $company->company_logo = NULL;
            }
            if($company->save(false)){
                //save owner
                $owner = new Users();
                $owner->role_id = Yii::$app->params['USER_ROLES']['owner'];
                $owner->company_id = $company->id;
                $owner->email = $email;
                $owner->mobile = $mobile;
                $owner->name = $owner_name;
                $owner->address = $address;
                $owner->latitude = $latitude;
                $owner->longitude = $longitude;
                $owner->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
                //save owner image.
                if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name']))
                {
                    $owner->profile_pic = time().$_FILES['profile_pic']['name'];
                    move_uploaded_file($_FILES['profile_pic']['tmp_name'],Yii::$app->params['DOCUMENT_ROOT'].'uploads/profile_pic/'.$owner->profile_pic);
                }
                else
                {
                    $owner->profile_pic = NULL;
                }
                
            
                if ($owner->save(false)) {
                    if (!empty($owner->email)) {
                        $emailformatemodel = EmailFormat::findOne(["id" => Yii::$app->params['EMAIL_TEMPLATE_ID']['welcome'], "status" => '1']);
                        if ($emailformatemodel) {
                            //create email body
                            $AreplaceString = array('{name}' => $owner->name, '{verification_link}' => Url::to('@siteRoot/site/reset-app-password?token=' . $owner->password_reset_token, true));
                            $body = CommonApiHelper::MailTemplate($AreplaceString, $emailformatemodel->body);
                            $ssSubject = $emailformatemodel->subject;
                            //send email to new registered user
                            Yii::$app->mailer->compose()
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                ->setTo($owner->email)
                                ->setSubject($ssSubject)
                                ->setHtmlBody($body)
                                ->send();
                        }
                    }
                    $transaction->commit();
                    return CommonApiHelper::return_success_response("You have been registered successfully, please check your inbox to verify account.", $response);
                }
            }
            $transaction->rollback();
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        /*} catch (\Exception $e) {
            $transaction->rollback();
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }*/
    }
}
