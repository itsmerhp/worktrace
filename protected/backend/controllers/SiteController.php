<?php
namespace backend\controllers;

use common\models\LoginForm;
use common\models\Users;
use common\models\ChangePasswordForm;
use common\models\ForgotPasswordForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use common\models\Pages;

/**
 * Site controller.
 * It is responsible for displaying static pages, and logging users in and out.
 */
class SiteController extends Controller
{
    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','cms','terms-condition'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','change-password','cms'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }
        
    /**
     * Declares external actions for the controller.
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays the index (home) page.
     * Use it in case your home page contains static content.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in the user if his account is activated,
     * if not, displays standard error message.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = "login";
        
        if (!Yii::$app->user->isGuest) 
        {
            return $this->goHome();
        }
        
        $model = new LoginForm() ;
        //set scenario for this model
        $forgotPasswordModel = new ForgotPasswordForm();
        
        //ajax validation code start
        if (Yii::$app->request->isAjax && $forgotPasswordModel->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($forgotPasswordModel);
        }
        
        // everything went fine, log in the user
        if ($model->load(Yii::$app->request->post()) && $model->login()) 
        {
            return $this->goBack();
        }
        //code for forgot password
        elseif ($forgotPasswordModel->load(Yii::$app->request->post()) && $forgotPasswordModel->validate())
        {
            //find user based on email id
            $userdata = Users::findOne(['email' => $forgotPasswordModel->email, 'status' => '1']);
            if (!empty($userdata))
            {
                $userdata->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
                if ($userdata->save(false))
                {
                    $userdata->sendPasswordResetEmail($userdata);
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Reset Password link has been sent to your registered email id.'));
                return $this->goBack();                
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Email id is not registered or User has been inactive.'));
                return $this->goBack();                
            }
        } 
        // errors will be displayed
        else 
        {
            return $this->render('login', compact('model', 'forgotPasswordModel'));
        }
    }
    
    /**
     * Web view for CMS pages.
     *
     * @return string
     */
    public function actionCms($id){
        $this->layout = "login";        
        $cmsPage =  Pages::findOne(['id'=>$id,'status'=>1]);
        return Yii::$app->controller->render('cms',['model'=>$cmsPage]);
    }
	
	/**
     * Web view for CMS pages.
     *
     * @return string
     */
    public function actionTermsCondition(){
        $this->layout = "login";        
        $cmsPage =  Pages::findOne(['id'=>2,'status'=>1]);
        return Yii::$app->controller->render('cms',['model'=>$cmsPage]);
    }
    
    /**
     * User can change password.
     *
     * @return string|\yii\web\Response
     */
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        //ajax validation code start
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
        //exit;
        // set data into model and validate model
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            // Get user details
            $usermodel           = \common\models\Users::findOne(Yii::$app->user->id);
            //set password
            $usermodel->password = password_hash($model->newPassword, PASSWORD_BCRYPT);
            //save password
            $usermodel->save(false);

            Yii::$app->session->setFlash('success', Yii::t('app', 'Your password has been changed successfully.'));
            return $this->redirect(Yii::$app->urlManager->createUrl(['site/login']));
        }
        else
        {
            return $this->render('change-password', compact('model'));
        }
        return $this->render('change-password', compact('model'));
    }

    /**
     * Logs out the user.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
