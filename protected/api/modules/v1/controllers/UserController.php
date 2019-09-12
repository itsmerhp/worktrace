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
class UserController extends Controller {

    // if you are doing non-verified stuff must have this set to false
    // so yii doesn't look for the token.

    public $enableCsrfValidation = false;

    public function init() {
        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);

        parent::init();

        Yii::$app->user->enableSession = false;    // no sessions for this controller
        Yii::$app->user->loginUrl = null;     // no default login needed
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;    // default this controller to JSON, otherwise it's FORMAT_HTML
    }

    public function actionCreateJwtToken() {
        $token = Yii::$app->jwt->getBuilder()
                ->setIssuer('http://www.worktrace.co.za/') // Configures the issuer (iss claim)
                ->setAudience('http://www.worktrace.co.za/') // Configures the audience (aud claim)
                ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                //->setNotBefore(time() + 60) // Configures the time before which the token cannot be accepted (nbf claim)
                ->setExpiration(time() + 120) // Configures the expiration time of the token (exp claim)
                ->set('uid', 100) // Configures a new claim, called "uid"
                ->getToken(); // Retrieves the generated token


        $token->getHeaders(); // Retrieves the token headers
        $token->getClaims(); // Retrieves the token claims

        echo 'jti' . $token->getHeader('jti'); // will print "4f1g23a12aa"
        echo '<br/>iss : ' . $token->getClaim('iss'); // will print "http://example.com"
        echo '<br/>uid : ' . $token->getClaim('uid'); // will print "1"
        echo '<br/>token : ' . $token;
        exit; // The string representation of the object is a JWT string (pretty easy, right?)
    }

    public function actionGetJwtToken() {
        $access_token = Yii::$app->request->headers->get('Authorization');
        $token = Yii::$app->jwt->getParser()->parse((string) trim(str_replace('bearer', '', $access_token))); // Parses from a string

        /* $token->getHeaders(); // Retrieves the token header
          $token->getClaims(); // Retrieves the token claims

          echo 'jti : '.$token->getHeader('jti'); // will print "4f1g23a12aa"
          echo '<br/>iss : '.$token->getClaim('iss'); // will print "http://example.com"
          echo '<br/>uid : '.$token->getClaim('uid');exit; // will print "1" */
        $data = Yii::$app->jwt->getValidationData(); // It will use the current time to validate (iat, nbf and exp)
        //$data->setIssuer('http://example.com');
        //$data->setAudience('http://example.org');
        //$data->setId('4f1g23a12aa');
        echo "<pre>";
        var_dump($token->validate($data));
        exit;
    }

    /**
     * To get new access token using refresh token
     *
     * @return     array
     */
    public function actionGetAccessToken() {
        try {
            $refreshToken = Yii::$app->request->headers->get('refresh_token');
            if($refreshToken){
                $response = [];
                $userDetails = UsersAccessTokens::find()
                        ->with(['user' => function ($query) {
                                $query->with(['company']);
                                return $query;
                            }])->where(['refresh_token' => $refreshToken])
                        ->one();
                
                if ($userDetails) {
                    if ($userDetails->user->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])) {
                        return CommonApiHelper::return_error_response("Your account is Inactive. Please contact Administrator for more details.", "-1");
                    } else if ($userDetails->user->company->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])) {
                        return CommonApiHelper::return_error_response("Company is Inactive. Please contact Administrator for more details.", "-1");
                    } else {
                        //generate new refresh token and update it
                        $refreshToken = Yii::$app->security->generateRandomString(255);
                        $userDetails->refresh_token = $refreshToken;
                        $userDetails->save(false);
                        
                        $data = [
                            'new_refresh_token' => $refreshToken,
                            'new_access_token' => CommonApiHelper::generateAccessToken($userDetails->user->user_id, $refreshToken)
                        ];

                        $response[] = $data;
                        return CommonApiHelper::return_success_response("", $response);
                    }
                } else {
                    return CommonApiHelper::return_error_response("Please pass valid refresh token.", "-4");
                }
            }else{
                return CommonApiHelper::return_error_response("Please pass refresh token.", "-4");
            }
        } catch (\Exception $e) {
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }

    /**
     * this function used by the application user for login purpose.
     */
    public function actionLogin() {
        //validate webservice
        $requiredParams = ['email', 'password', 'device_type'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);
        $device_token = !empty($post['device_token']) ? $post['device_token'] : '';
        $device_type = !empty($post['device_type']) ? $post['device_type'] : '';

        try {
            //Fetch user details
            $userdata = Users::find()->with(['company'])->where(['email' => $post['email']])->one();

            if (!empty($userdata)) {
                if ($userdata->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])) {
                    return CommonApiHelper::return_error_response("Your account is Inactive. Please contact Administrator for more details.", "-1");
                } else if ($userdata->company->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])) {
                    return CommonApiHelper::return_error_response("Company is Inactive. Please contact Administrator for more details.", "-1");
                } else {
                    if (!empty($post['password']) && password_verify($post['password'], $userdata->password)) {
                        //Save/update access token of user
                        $refresh_token = Yii::$app->security->generateRandomString(255);
                        $access_token = UsersAccessTokens::find()->where(['user_id' => $userdata->user_id, 'device_token' => $device_token])->one();
                        if (empty($access_token)) {
                            $access_token = new UsersAccessTokens();
                            $access_token->user_id = $userdata->user_id;
                            $access_token->device_type = !empty($device_type) ? $device_type : Yii::$app->params['DEVICE_TYPE']['android'];
                            $access_token->device_token = !empty($post['device_token']) ? $post['device_token'] : '';
                        }
                        $access_token->refresh_token = $refresh_token;
                        $access_token->save(false);

                        //Fetch company details
                        $company_details = [];
                        if ($userdata->company) {
                            $company_details = [
                                "id" => $userdata->company->id,
                                "name" => $userdata->company->name,
                                "email" => $userdata->company->email,
                                "mobile" => $userdata->company->mobile,
                                "address" => $userdata->company->address,
                                "company_logo" => $userdata->company->company_logo ? Yii::getAlias('@host') . '/uploads/profile_pic/' . $userdata->company->company_logo : '',
                                "latitude" => $userdata->company->latitude,
                                "longitude" => $userdata->company->longitude
                            ];
                        }

                        $data = [
                            'user_id' => $userdata->user_id,
                            'role_id' => $userdata->role_id,
                            'company_id' => $userdata->company_id,
                            'name' => $userdata->name,
                            'email' => $userdata->email,
                            'mobile' => $userdata->mobile,
                            'address' => $userdata->address,
                            'latitude' => $userdata->latitude,
                            'longitude' => $userdata->longitude,
                            'profile_pic' => !empty($userdata->profile_pic) ? Yii::getAlias('@host') . '/uploads/profile_pic/' . $userdata->profile_pic : '',
                            'company_details' => $company_details,
                            'access_token' => CommonApiHelper::generateAccessToken($userdata->user_id, $refresh_token),
                            'refresh_token' => $refresh_token,
                        ];

                        $response[] = $data;
                        return CommonApiHelper::return_success_response("Login successfully.", $response);
                    } else {
                        return CommonApiHelper::return_error_response("Please enter correct email or password", "2");
                    }
                }
            } else {
                return CommonApiHelper::return_error_response("Your are not registered, please signup.", "4");
            }
        } catch (\Exception $e) {
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }

    /**
     * This funciton is user for the forgot password api
     *
     * @return     array
     */
    public function actionForgotPassword() {
        //validate webservice
        $requiredParams = ['email'];
        try {
            CommonApiHelper::validateRequestParameters($requiredParams);

            $response = [];
            $post = Yii::$app->request->bodyParams;
            $post = array_map('trim', $post);
            //Fetch user details
            $userdata = Users::findOne(['email' => $post['email'], 'status' => '1']);
            if (!empty($userdata)) {
                $userdata->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
                if ($userdata->save(false)) {
                    Users::sendPasswordResetEmail($userdata);
                }
                return CommonApiHelper::return_success_response("Reset Password link has been sent to your registered email id.", []);
            } else {
                return CommonApiHelper::return_error_response("Email is not registered or User is inactive.", "2");
            }
        } catch (\Exception $e) {
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }

}
