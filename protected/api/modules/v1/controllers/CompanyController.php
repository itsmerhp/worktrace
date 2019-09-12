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
use common\models\Departments;

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
        $validateActions = ['subscription-status' => 'all','add-edit-department' => 'owner','get-departments' => 'owner'];
        if(in_array($actionName, array_keys($validateActions))){
            $this->userDetails = CommonApiHelper::validateAccessToken();   
            $allowedRole = $validateActions[$actionName];
            if(!($this->userDetails && ($allowedRole == 'all' || (Yii::$app->params['USER_ROLES'][$allowedRole] == $this->userDetails->user->role_id)))){
                CommonApiHelper::encodeResponseJSON(CommonApiHelper::return_error_response("You don't have rights to perform this action.", "-5"));
            }
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
    
    /**
     * Owner > Add / Edit department
     */
    public function actionAddEditDepartment() {
        //validate webservice
        $requiredParams = ['name'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];

        try {
            $transaction = Yii::$app->db->beginTransaction();
            //Get request parameters.
            $post = Yii::$app->request->bodyParams;
            $post = array_map('trim', $post);

            $name = $post['name'];
            $department_id = isset($post['department_id']) && !empty($post['department_id']) ? $post['department_id'] : NULL;
            
            //save / update company
            if(!empty($department_id)){
                $department = Departments::find()->where(['id' => $department_id])->one();
                $department->status = isset($post['status']) ? $post['status'] : $department->status;
            }else{
                $department = new Departments();
                $department->company_id = $this->userDetails->user->company->id;
                $department->status = isset($post['status']) ? $post['status'] : array_search('Active', Yii::$app->params['STATUS_SELECT']);
            }
            
            $department->name = $name;
            
            if ($department->save(false)) {
                $transaction->commit();
                $response[] = [
                    'department_id' => $department->id,
                    'name' => $department->name,
                ];
                $successMessage = "New department has been ".($department_id?"updated" : "created")." successfully.";
                return CommonApiHelper::return_success_response($successMessage, $response);
            }
            $transaction->rollback();
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
       } catch (\Exception $e) {
            $transaction->rollback();
            return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }
    
    /**
     * Owner > get department list
     */
    public function actionGetDepartments() {
        try {
            //Fetch departments of company
            $departments = Departments::find()->select(['id as department_id','name'])->where(['company_id' => $this->userDetails->user->company->id,'status' => array_search('Active', Yii::$app->params['STATUS_SELECT'])])->asArray()->all();
            if(!empty($departments)){                
                return CommonApiHelper::return_success_response("", $departments);
            }else{
                return CommonApiHelper::return_error_response("No department found.","2");
            }
        } catch (\Exception $e) {
             return CommonApiHelper::return_error_response("Sorry, Please try again.");
        }
    }
}
