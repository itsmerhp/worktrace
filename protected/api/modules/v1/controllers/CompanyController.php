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
class CompanyController extends Controller {

    // if you are doing non-verified stuff must have this set to false
    // so yii doesn't look for the token.

    public $enableCsrfValidation = false;
    public $userDetails;
    
    public function init() {
        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);

        parent::init();

        Yii::$app->user->enableSession = false;    // no sessions for this controller
        Yii::$app->user->loginUrl = null;     // no default login needed
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;    // default this controller to JSON, otherwise it's FORMAT_HTML
    }

    public function beforeAction($action) {
        $actionName = $action->id;
        $validateActions = ['subscription-status'];
        if(in_array($actionName, $validateActions)){
            $this->userDetails = CommonApiHelper::validateAccessToken();            
        }
        return parent::beforeAction($action);
    }

    /**
     * this function used by the application user to get company subscription details.
     */
    public function actionSubscriptionStatus() {
                
        $response = [];
        try {
            if (!empty($this->userDetails)) {
                if ($this->userDetails->user->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])) {
                    return CommonApiHelper::return_error_response("Your account is Inactive. Please contact Administrator for more details.", "-1");
                }else if($this->userDetails->user->company->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])){
                    return CommonApiHelper::return_error_response("Company is Inactive. Please contact Administrator for more details.", "-1");
                }else{
                    return CommonApiHelper::return_success_response("", $response);
                }
            } else {
                return CommonApiHelper::return_error_response("Sorry, Please try again.");
            }
        } catch (\Exception $e) {
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }

}
