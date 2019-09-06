<?php

namespace backend\controllers;

use Yii;
use common\models\RestaurantOtp;
use common\models\RestaurantOtpSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\EmailFormat;
use yii\helpers\ArrayHelper;
use api\components\CommonApiHelper;

/**
 * RestaurantOtpController implements the CRUD actions for RestaurantOtp model.
 */
class RestaurantOtpController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RestaurantOtp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RestaurantOtpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RestaurantOtp model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RestaurantOtp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RestaurantOtp();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
			//Generate OTP
			$model->restaurant_otp = mt_rand(100000, 999999);
			$model->status = 1;
			$model->save(false);
			
			//Send OTP on restaurant email id
			$emailformatemodel = EmailFormat::findOne(["id" => Yii::$app->params['EMAIL_TEMPLATE_ID']['restaurant_otp'], "status" => '1']);
			if ($emailformatemodel)
			{
				//create email body
				$AreplaceString = array('{OTP}' => $model->restaurant_otp);
				$body       = CommonApiHelper::MailTemplate($AreplaceString, $emailformatemodel->body);
				$ssSubject  = $emailformatemodel->subject;
				//send email to new registered user
				Yii::$app->mailer->compose()
					->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
					->setTo($model->email)
					->setSubject($ssSubject)
					->setHtmlBody($body)
					->send(); 
			}			
			Yii::$app->session->setFlash('success', Yii::t('app', 'OTP has been sent on Restaurant email.'));
			return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RestaurantOtp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RestaurantOtp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RestaurantOtp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RestaurantOtp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RestaurantOtp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
			
	/**
     * active / Inactive the users from the index page.
     * @param      id int 
     * @return     
     */
    public function actionActive($id)
    {
        $model = $this->findModel($id);
        $model->status = ($model->status == '1') ? '0' : '1';
        $model->save(false);
        $message = ($model->status == '1') ? "Restaurant OTP has been activated successfully.": "Restaurant OTP has been inactivated successfully.";
		Yii::$app->session->setFlash('success', $message);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
