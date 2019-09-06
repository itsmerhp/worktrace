<?php

namespace backend\controllers;

use Yii;
use common\models\Meals;
use common\models\MealsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * MealsController implements the CRUD actions for Meals model.
 */
class MealsController extends Controller
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
			'uploadPhoto' => [
                'class' => 'budyaga\cropper\actions\UploadAction',
                'url' => \Yii::getAlias("@host").'/uploads/meals',
                'path' => \Yii::$app->params['uploads_path'].'meals/',
				'maxSize'	=>	5242880,
				'width'	=>	500,
				'height'	=>	500,
            ]
        ];
    }

    /**
     * Lists all Meals models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MealsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Meals model.
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
     * Creates a new Meals model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Meals();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
			if(!empty($model->image)){
				//S3 bucket config
				$s3 = S3Client::factory([
					'version'     => 'latest',
					'region'      => 'us-east-1',
					'credentials' => [
						'key'    => Yii::$app->params['AWS_KEY'],
						'secret' => Yii::$app->params['AWS_SECRET'],
					]
				]);

				//create key for filename
				$fileDetails = pathinfo($model->image);
				$fileName = $fileDetails['basename'];
				$ext = $fileDetails['extension'];
				//Create key, upload image and set the permission for the file over amazon s3 bucket
				$uploadPostImage = $s3->putObject(array(
					'Bucket'     => Yii::$app->params['AWS_BUCKET'],
					'Key'        => 'meals/'.$fileName,
					'Body'       => file_get_contents($model->image), //remote URL
					'ACL'        => 'public-read', //for making the public url
					'ContentType'=> 'image/'.$ext,
				));

				//get amazon s3 bucket URL for post image
				$model->image = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'meals/'.$fileName);
				$model->save(false);
			}
            Yii::$app->session->setFlash('success', Yii::t('app', 'Meal has been created successfully.'));
			return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Meals model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
		$oldImage = $model->image;
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
			if(!empty($model->image) && $oldImage != $model->image){
				//S3 bucket config
				$s3 = S3Client::factory([
					'version'     => 'latest',
					'region'      => 'us-east-1',
					'credentials' => [
						'key'    => Yii::$app->params['AWS_KEY'],
						'secret' => Yii::$app->params['AWS_SECRET'],
					]
				]);

				//create key for filename
				$fileDetails = pathinfo($model->image);
				$fileName = $fileDetails['basename'];
				$ext = $fileDetails['extension'];
				//Create key, upload image and set the permission for the file over amazon s3 bucket
				$uploadPostImage = $s3->putObject(array(
					'Bucket'     => Yii::$app->params['AWS_BUCKET'],
					'Key'        => 'meals/'.$fileName,
					'Body'       => file_get_contents($model->image), //remote URL
					'ACL'        => 'public-read', //for making the public url
					'ContentType'=> 'image/'.$ext,
				));

				//get amazon s3 bucket URL for post image
				$model->image = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'meals/'.$fileName);
				$model->save(false);
			}
            Yii::$app->session->setFlash('success', Yii::t('app', 'Meal has been updated successfully.'));
			return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Meals model.
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
     * Finds the Meals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meals::findOne($id)) !== null) {
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
        $message = ($model->status == '1') ? "Meal has been activated successfully.": "Meal has been inactivated successfully.";
		Yii::$app->session->setFlash('success', $message);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
