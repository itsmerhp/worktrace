<?php

namespace backend\controllers;

use Yii;
use common\models\Dishes;
use common\models\DishMedia;
use common\models\Meals;
use common\models\DishesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use yii\web\UploadedFile;
use FFMpeg;

/**
 * DishesController implements the CRUD actions for Dishes model.
 */
class DishesController extends Controller
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
                'url' => \Yii::getAlias("@host").'/uploads/dishes',
                'path' => \Yii::$app->params['uploads_path'].'dishes/',
				'maxSize'	=>	5242880,
				'width'	=>	500,
				'height'	=>	500,
            ]
        ];
    }

    /**
     * Lists all Dishes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DishesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dishes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Dishes::find()
							->select([
								'dishes.*',
								'IFNULL(avg(ratings.dish_rating),0) as avg_dish_rating',
								'IFNULL(avg(ratings.quality_rating),0) as avg_quality_rating',
								'IFNULL(avg(ratings.appearance_rating),0) as avg_appearance_rating',
								'count(ratings.id) as total_ratings'
							])
							->leftJoin('ratings','ratings.dish_id = dishes.id')
							->groupBy(['dishes.id'])
							->with(['user' => function($query) {
								$query->with([
									'restaurantDetails' =>  function($query) {
										$query->with(['country']);
									}
								]);
							},
							'dishMedia'])
							->joinWith(['user'])
							->joinWith(['cuisine','meal','quickFood'])
							->where(['dishes.id' => $id])
							->one(),
        ]);
    }

    /**
     * Creates a new Dishes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dishes();
		$model->scenario = 'create';
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
		
		$validationError = 0;
        if ($model->load(Yii::$app->request->post())) {
			//Upload video
			$dishVideo = UploadedFile::getInstance($model, 'dish_video');
			if(!empty($dishVideo) && $model->dish_video_duration > 10){
				$validationError = 1;
				$model->addError('dish_video', 'Video length should not exceed 10 seconds.');				
				$model->dish_video = '';
				$model->dish_images = '';
			}
			
			//Upload images
			$dish_images = UploadedFile::getInstances($model, 'dish_images');
			if(empty($dish_images)){
				$validationError = 1;
				$model->addError('dish_images', 'Please upload dish images.');				
				$model->dish_video = '';
				$model->dish_images = '';
			}
		
			if($validationError == 0 && $model->save(false)){				
				//S3 bucket config
				$s3 = S3Client::factory([
					'version'     => 'latest',
					'region'      => 'us-east-1',
					'credentials' => [
						'key'    => Yii::$app->params['AWS_KEY'],
						'secret' => Yii::$app->params['AWS_SECRET'],
					]
				]);
				$bucketDishImages = [];
				if(!empty($dish_images)){					
					foreach($dish_images as $dishImageDetails){
						//create key for filename
						$fileDetails = pathinfo($dishImageDetails->name);						
						$ext = $fileDetails['extension'];
						$fileName = 'IMG_DISH_'.time().'_'.mt_rand(100000, 999999).'.'.$ext;
						//Create key, upload image and set the permission for the file over amazon s3 bucket
						$uploadPostImage = $s3->putObject(array(
							'Bucket'     => Yii::$app->params['AWS_BUCKET'],
							'Key'        => 'dish_images/'.$fileName,
							'Body'       => file_get_contents($dishImageDetails->tempName), //remote URL
							'ACL'        => 'public-read', //for making the public url
							'ContentType'=> 'image/'.$ext,
						));

						//get amazon s3 bucket URL for dish image
						$bucketDishImages[] = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_images/'.$fileName);
					}
				}
				$bucketDishVideo = $bucketDishVideoThumbnail = '';
				if(!empty($dishVideo)){
					//create key for filename
					$fileDetails = pathinfo($dishVideo->name);						
					$ext = $fileDetails['extension'];
					$fileName = 'VIDEO_DISH_'.time().'_'.mt_rand(100000, 999999).'.'.$ext;
					//Create key, upload dish video and set the permission for the file over amazon s3 bucket
					$uploadPostImage = $s3->putObject(array(
						'Bucket'     => Yii::$app->params['AWS_BUCKET'],
						'Key'        => 'dish_videos/'.$fileName,
						'Body'       => file_get_contents($dishVideo->tempName), //remote URL
						'ACL'        => 'public-read', //for making the public url
						'ContentType'=> 'video/'.$ext,
					));
					//get amazon s3 bucket URL for post image
					$bucketDishVideo = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_videos/'.$fileName);
					
					//generate video thumbnail
					$thumbnailName = 'VIDEO_THUMBNAIL_DISH_'.time().'_'.mt_rand(100000, 999999).'.png';
					$thumbnail = Yii::$app->params['DOCUMENT_ROOT'].'uploads/dishes/'.$thumbnailName;

					$ffmpeg = FFMpeg\FFMpeg::create();
					$video = $ffmpeg->open($dishVideo->tempName);
					$video->filters()
								->resize(new FFMpeg\Coordinate\Dimension(500, 500))
								->synchronize();
					$video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3))->save($thumbnail);
					
					//Create key, upload dish video thumbnail and set the permission for the file over amazon s3 bucket
					$uploadPostImage = $s3->putObject(array(
						'Bucket'     => Yii::$app->params['AWS_BUCKET'],
						'Key'        => 'dish_images/'.$thumbnailName,
						'Body'       => file_get_contents($thumbnail), //remote URL
						'ACL'        => 'public-read', //for making the public url
						'ContentType'=> 'image/png',
					));
					//get amazon s3 bucket URL for post image
					$bucketDishVideoThumbnail = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_images/'.$thumbnailName);
				}
				   
				//Batch insert dishimages
			   	$insertDishMedia = [];			
				$currentDate = date("Y-m-d H:i:s");
				if(!empty($bucketDishImages)){
					//Prepare array for batch insert dish images		
					foreach($bucketDishImages as $resDishImage){
						if(!empty(trim($resDishImage))){
							$insertDishMedia[] = [
								$model->id,
								1,
								trim($resDishImage),
								NULL,
								$currentDate,
								$currentDate
							];
						}
					}
					
				}
				   
			   if(!empty($bucketDishVideo)){
					$insertDishMedia[] = [
						$model->id,
						2,
						trim($bucketDishVideo),
						trim($bucketDishVideoThumbnail),
						$currentDate,
						$currentDate
					];
				}
				if(!empty($insertDishMedia)){
					//batch insert dish images
					Yii::$app->db->createCommand()->batchInsert('dish_media', ['dish_id', 'type', 'url', 'thumb_url', 'created_at', 'updated_at'], $insertDishMedia)->execute();
				}
				Yii::$app->session->setFlash('success', Yii::t('app', 'Dish has been created successfully.'));
				return $this->redirect(['index']);
			}	
		}
		return $this->render('create', [
			'model' => $model,
		]);
    }

    /**
     * Updates an existing Dishes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //$model = $this->findModel($id);
		$model = Dishes::find()->with(['dishMedia'])->where(['id' => $id])->one();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
			$validationError = 0;
			//video validation
			$dishVideo = UploadedFile::getInstance($model, 'dish_video');
			if(!empty($dishVideo) && $model->dish_video_duration > 10){
				$validationError = 1;
				$model->addError('dish_video', 'Video length should not exceed 10 seconds.');
			}
			if($validationError == 0 && $model->save(false)){				
				//S3 bucket config
				$s3 = S3Client::factory([
					'version'     => 'latest',
					'region'      => 'us-east-1',
					'credentials' => [
						'key'    => Yii::$app->params['AWS_KEY'],
						'secret' => Yii::$app->params['AWS_SECRET'],
					]
				]);
				$bucketDishImages = [];
				$dish_images = UploadedFile::getInstances($model, 'dish_images');
				if(!empty($dish_images)){		
					//Remove existing dish images
					DishMedia::deleteAll(['dish_id'=>$model->id,'type' => 1]);
					foreach($dish_images as $dishImageDetails){
						//create key for filename
						$fileDetails = pathinfo($dishImageDetails->name);						
						$ext = $fileDetails['extension'];
						$fileName = 'IMG_DISH_'.time().'_'.mt_rand(100000, 999999).'.'.$ext;
						//Create key, upload image and set the permission for the file over amazon s3 bucket
						$uploadPostImage = $s3->putObject(array(
							'Bucket'     => Yii::$app->params['AWS_BUCKET'],
							'Key'        => 'dish_images/'.$fileName,
							'Body'       => file_get_contents($dishImageDetails->tempName), //remote URL
							'ACL'        => 'public-read', //for making the public url
							'ContentType'=> 'image/'.$ext,
						));

						//get amazon s3 bucket URL for dish image
						$bucketDishImages[] = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_images/'.$fileName);
					}
				}
				$bucketDishVideo = $bucketDishVideoThumbnail = '';
				if(!empty($dishVideo)){
					//Remove existing dish video
					DishMedia::deleteAll(['dish_id'=>$model->id,'type' => 2]);
					
					//create key for filename
					$fileDetails = pathinfo($dishVideo->name);						
					$ext = $fileDetails['extension'];
					$fileName = 'VIDEO_DISH_'.time().'_'.mt_rand(100000, 999999).'.'.$ext;
					//Create key, upload dish video and set the permission for the file over amazon s3 bucket
					$uploadPostImage = $s3->putObject(array(
						'Bucket'     => Yii::$app->params['AWS_BUCKET'],
						'Key'        => 'dish_videos/'.$fileName,
						'Body'       => file_get_contents($dishVideo->tempName), //remote URL
						'ACL'        => 'public-read', //for making the public url
						'ContentType'=> 'video/'.$ext,
					));
					//get amazon s3 bucket URL for post image
					$bucketDishVideo = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_videos/'.$fileName);
					
					//generate video thumbnail
					$thumbnailName = 'VIDEO_THUMBNAIL_DISH_'.time().'_'.mt_rand(100000, 999999).'.png';
					$thumbnail = Yii::$app->params['DOCUMENT_ROOT'].'uploads/dishes/'.$thumbnailName;

					$ffmpeg = FFMpeg\FFMpeg::create();
					$video = $ffmpeg->open($dishVideo->tempName);
					$video->filters()
								->resize(new FFMpeg\Coordinate\Dimension(500, 500))
								->synchronize();
					$video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3))->save($thumbnail);
					
					//Create key, upload dish video thumbnail and set the permission for the file over amazon s3 bucket
					$uploadPostImage = $s3->putObject(array(
						'Bucket'     => Yii::$app->params['AWS_BUCKET'],
						'Key'        => 'dish_images/'.$thumbnailName,
						'Body'       => file_get_contents($thumbnail), //remote URL
						'ACL'        => 'public-read', //for making the public url
						'ContentType'=> 'image/png',
					));
					//get amazon s3 bucket URL for post image
					$bucketDishVideoThumbnail = $s3->getObjectUrl(Yii::$app->params['AWS_BUCKET'], 'dish_images/'.$thumbnailName);
				}
				   
				//Batch insert dishimages
			   	$insertDishMedia = [];									
				$currentDate = date("Y-m-d H:i:s");
				if(!empty($bucketDishImages)){
					//Prepare array for batch insert dish images
					foreach($bucketDishImages as $resDishImage){
						if(!empty(trim($resDishImage))){
							$insertDishMedia[] = [
								$model->id,
								1,
								trim($resDishImage),
								NULL,
								$currentDate,
								$currentDate
							];
						}
					}
					
				}
				   
			   if(!empty($bucketDishVideo)){
					$insertDishMedia[] = [
						$model->id,
						2,
						trim($bucketDishVideo),
						trim($bucketDishVideoThumbnail),
						$currentDate,
						$currentDate
					];
				}
				if(!empty($insertDishMedia)){
					//batch insert dish images
					Yii::$app->db->createCommand()->batchInsert('dish_media', ['dish_id', 'type', 'url', 'thumb_url', 'created_at', 'updated_at'], $insertDishMedia)->execute();
				}
				Yii::$app->session->setFlash('success', Yii::t('app', 'Dish has been created successfully.'));
				return $this->redirect(['index']);
			}	
        } 
		$model->dish_video = '';
		$model->dish_images = '';
		if(!empty($model->dishMedia)){
			foreach($model->dishMedia as $mediaDetails){
				if($mediaDetails->type==1){
					$model->dish_images[] = $mediaDetails->url;
				}else if($mediaDetails->type==2){
					$model->dish_video = $mediaDetails->url;
				}
			}
		}
		return $this->render('update', [
			'model' => $model,
		]);
    }

    /**
     * Deletes an existing Dishes model.
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
     * Finds the Dishes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dishes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dishes::findOne($id)) !== null) {
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
        $message = ($model->status == '1') ? "Dish has been activated successfully.": "Dish has been inactivated successfully.";
		Yii::$app->session->setFlash('success', $message);
        return $this->redirect(Yii::$app->request->referrer);
    }
	
	/**
     * Select meals based on selection of cusine
     */
    public function actionListMeals($id)
    {
		//Fetch meals for selected cusine
		$listMeals = Meals::find()
			 ->where(['cuisine_id' => $id,'status'=>1])
			 ->orderBy('name asc')
			 ->all();
		
		//Create options HTML for Meals
		echo "<option value=''>Select Meal</option>";
		if(!empty($listMeals)){
			foreach($listMeals as $resMeal){
				echo "<option data-img-src = '".$resMeal->image."' value='".$resMeal->id."'>".$resMeal->name."</option>";
			}
		}
    }
}
