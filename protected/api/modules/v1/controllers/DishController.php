<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use common\models\Users;
use yii\helpers\Url;
use common\components\Common;
use api\components\CommonApiHelper;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\models\EmailFormat;
use common\models\UsersAccessTokens;
use common\models\Restaurants;
use common\models\RestaurantOtp;
use common\models\Cuisines;
use common\models\Meals;
use common\models\QuickFoods;
use common\models\Dishes;
use common\models\Ratings;
use common\models\DishMedia;

/**
 * Simple controller, all response are JSON type.
 */
class DishController extends Controller
{

    // if you are doing non-verified stuff must have this set to false
    // so yii doesn't look for the token.

    public $enableCsrfValidation = false;

    public function init()
    {
        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);
        
        if(isset($post['user_id'])){
			$accessToken = Yii::$app->request->headers->get('access_token');
            CommonApiHelper::checkUserStatus($post['user_id'], $accessToken);
        }
		
		//Check post exist or not.
        if(isset($post['post_id']) && !empty ($post['post_id'])){
            CommonApiHelper::postExistanceCheck($post['post_id']);
        }
        
        parent::init();
        
        Yii::$app->user->enableSession = false;    // no sessions for this controller
        Yii::$app->user->loginUrl      = null;     // no default login needed
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;    // default this controller to JSON, otherwise it's FORMAT_HTML
    }

    public function actionIndex()
    {
        $response           = [];
        $response['params'] = Yii::$app->request->bodyParams;
        $response['token']  = uniqid();
        return $response;
    }
	
	/**
     * This function will used get Cuisines.
     */
    public function actionListCuisines()
    {
		//validate webservice
        //$requiredParams = ['user_id'];
        
        //CommonApiHelper::validateRequestParameters($requiredParams);
        try{
			$response = [];

			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);	
			
			//fetch cuisines list
			$cuisinesList = Cuisines::find()
								->select(['cuisines.id','cuisines.name','cuisines.image'])								
								->where(['status'=>1])
								->orderBy(['name'=>SORT_ASC])
								->asArray()
								->all();
			
			if(!empty($cuisinesList)){				
				return CommonApiHelper::return_success_response("","",$cuisinesList);	
			}else{
				return CommonApiHelper::return_error_response("No cuisine found.");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}	
    }
	
	/**
     * This function will used get Meals.
     */
    public function actionListMeals()
    {
		//validate webservice
        $requiredParams = ['cuisine_id'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        try{
			$response = [];

			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);
			$cuisineId = $post['cuisine_id'];
			
			//fetch meals list
			$mealsList = Meals::find()
								->select(['meals.id','meals.name','meals.image'])								
								->where(['status'=>1,'meals.cuisine_id' => $cuisineId])
								->orderBy(['name'=>SORT_ASC])
								->asArray()
								->all();
			
			if(!empty($mealsList)){				
				return CommonApiHelper::return_success_response("","",$mealsList);	
			}else{
				return CommonApiHelper::return_error_response("No meal found.");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}	
    }
	
	/**
     * This function will used get Quick Foods.
     */
    public function actionListQuickFoods()
    {
		//validate webservice
        //$requiredParams = ['user_id'];
        
        //CommonApiHelper::validateRequestParameters($requiredParams);
        try{
			$response = [];

			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);	
			
			//fetch Quick Foods list
			$quickFoodsList = QuickFoods::find()
								->select(['quick_foods.id','quick_foods.name','quick_foods.image'])								
								->where(['status'=>1])
								->orderBy(['name'=>SORT_ASC])
								->asArray()
								->all();
			
			if(!empty($quickFoodsList)){				
				return CommonApiHelper::return_success_response("","",$quickFoodsList);	
			}else{
				return CommonApiHelper::return_error_response("No quick food found.");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}	
    }
	
	/**
     * this function used by the application user for add dish
     */
    public function actionSaveDish()
    {
		//validate webservice
        $requiredParams = ['user_id','title','cuisine_id','meal_id','price','media'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$user_id = $post['user_id'];
			$title = $post['title'];
			$cuisine_id = $post['cuisine_id'];
			$meal_id = $post['meal_id'];
			$price = $post['price'];
			$media = $post['media'];
			$description = !empty($post['description']) ? $post['description'] : null ;
			$quick_food_id = !empty($post['quick_food_id']) ? $post['quick_food_id'] : null ;
			
			//Save dish
			$dishes               = new Dishes();
			$dishes->user_id      = $user_id;
			$dishes->title        = $title;
			$dishes->cuisine_id   = $cuisine_id;
			$dishes->meal_id      = $meal_id;
			$dishes->price        = $price;
			$dishes->description  = $description;
			$dishes->quick_food_id= $quick_food_id;			
			if($dishes->save(false)){
				//Batch insert dish media
				$dishMediaArray= [];
				$post['media'] = json_decode($post['media'], true);
				if(!empty($post['media'])){
					//Prepare array for batch insert media
					$insertMedia = [];
					$currentDate = date("Y-m-d H:i:s");
					foreach($post['media'] as $resMedia){
						if(!empty($resMedia)){
							$insertMedia[] = [
								$dishes->id,
								$resMedia['type'],
								$resMedia['url'],
								$resMedia['thumb_url'],
								$currentDate,
								$currentDate
							];
						}
					}
					if(!empty($insertMedia)){
						//batch insert media
						Yii::$app->db->createCommand()->batchInsert('dish_media', ['dish_id', 'type', 'url', 'thumb_url', 'created_at', 'updated_at'], $insertMedia)->execute();
					}
				}
			}
					
			return CommonApiHelper::return_success_response("Dish has been saved.","",[]);	
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user for update dish
     */
    public function actionUpdateDish()
    {
		//validate webservice
        $requiredParams = ['user_id','dish_id'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$user_id = $post['user_id'];
			$dish_id = $post['dish_id'];
			
			//Check if Dish exist or not
			//If exist then update
			$dishDetails = Dishes::find()->where(['user_id' => $user_id,'id' => $dish_id,'status' => 1])->one();
			if(!empty($dishDetails)){				
				$dishDetails->cuisine_id = isset($post['cuisine_id']) ? $post['cuisine_id']: $dishDetails->cuisine_id;
				$dishDetails->meal_id = isset($post['meal_id']) ? $post['meal_id']: $dishDetails->meal_id;
				$dishDetails->price = isset($post['price']) ? $post['price']: $dishDetails->price;
				$dishDetails->description = isset($post['description']) ? $post['description']: $dishDetails->description;
				$dishDetails->quick_food_id = isset($post['quick_food_id']) ? $post['quick_food_id']: $dishDetails->quick_food_id;
				$dishDetails->title = isset($post['title']) ? $post['title']: $dishDetails->title;
				
				if($dishDetails->save(false)){
					//Batch insert dish media
					$dishMediaArray= [];
					$post['media'] = isset($post['media']) ? json_decode($post['media'], true) : '';
					if(!empty($post['media'])){
						//Remove all media
						DishMedia::deleteAll(['dish_id'=>$dish_id]);
						
						//Prepare array for batch insert media
						$insertMedia = [];
						$currentDate = date("Y-m-d H:i:s");
						foreach($post['media'] as $resMedia){
							if(!empty($resMedia)){
								$insertMedia[] = [
									$dishDetails->id,
									$resMedia['type'],
									$resMedia['url'],
									$resMedia['thumb_url'],
									$currentDate,
									$currentDate
								];
							}
						}
						if(!empty($insertMedia)){
							//batch insert media
							Yii::$app->db->createCommand()->batchInsert('dish_media', ['dish_id', 'type', 'url', 'thumb_url', 'created_at', 'updated_at'], $insertMedia)->execute();
						}
					}
				}

				return CommonApiHelper::return_success_response("Dish has been updated.","",[]);	
			}else{
				return CommonApiHelper::return_error_response('No dish exist.','','2');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user to list dishes
	 * feed_type => 1 = Ranked View, 2 => Pull View / Single View
	 * location_type => 1 = Local, 2 => national
	 * cuisine_id => single
	 * meal_id => multiple comma seperated
	 * quick_food_id => multiple comma seperated
	 * search => To search by dish and restaurant name
     */
    public function actionListDishes()
    {
        //validate webservice
        $requiredParams = ['feed_type','latitude','longitude','country_code','page'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			$user_id = isset($post['user_id']) ? $post['user_id'] : '';
			$latitude = $post['latitude'];
			$longitude = $post['longitude'];
			$country_code = !empty($post['country_code'])?$post['country_code']:'US';
			$feed_type = $post['feed_type'];
			$location_type = isset($post['location_type']) ? $post['location_type'] : Yii::$app->params['LOCATION_TYPE']['local'];
			$cuisine_id = isset($post['cuisine_id']) ? $post['cuisine_id'] : '';
			$meal_id = isset($post['meal_id']) ? explode(',',$post['meal_id']) : [];
			$quick_food_id = isset($post['quick_food_id']) ? explode(',',$post['quick_food_id']) : [];
			$search = isset($post['search']) ? $post['search'] : '';
			$current_index = $post['page'];
			$random_offset = !empty($post['random_offset']) ? $post['random_offset'] : rand(99,999);
			
			//Records per page
			$per_page = Yii::$app->params['records_per_page'];
			
			//Fetch related details			
			if(!empty($user_id)){
				$relatedDetails = ['cuisine','meal','quickFood','dishMedia',
								   'userRating' =>  function($query)use($user_id) {
										$query->where(['user_id' => $user_id,'status' => 1]);
								 	},'user' =>  function($query) {
										$query->select(['user_id','name','profile_pic']);
								 	}
								  ];
			}else{
				$relatedDetails = ['cuisine','meal','quickFood','dishMedia','user' =>  function($query) {
										$query->select(['user_id','name','profile_pic']);
								 	}];
			}
			
			//Find list of dishes
			$listDishes = Dishes::find()
				->select([
					'IFNULL((((avg(ratings.dish_rating)/8)*(60/100)) + ((avg(ratings.quality_rating)/4)*(25/100)) + ((avg(ratings.appearance_rating)/4)*(15/100))),0) as rank',
					'(((acos(sin(('.$latitude.'*pi()/180)) * sin((restaurants.restaurant_lat*pi()/180))+cos(('.$latitude.'*pi()/180)) * cos((restaurants.restaurant_lat*pi()/180)) * cos((('.$longitude.'- restaurants.restaurant_long)*pi()/180))))*180/pi())*60*1.1515) as distance',
					'dishes.id','dishes.user_id','dishes.cuisine_id','dishes.meal_id',
					'dishes.quick_food_id','dishes.title','dishes.description','dishes.price',
					'IFNULL(avg(ratings.dish_rating),0) as avg_dish_rating','IFNULL(avg(ratings.quality_rating),0) as avg_quality_rating','IFNULL(avg(ratings.appearance_rating),0) as avg_appearance_rating','count(ratings.id) as total_ratings',
					'restaurants.name','restaurants.mobile','restaurants.address','restaurants.restaurant_lat','restaurants.restaurant_long',
					'restaurant_country.currency_symbol','restaurant_country.currency_code'
				])
				->with($relatedDetails)
				->leftJoin('ratings','ratings.dish_id = dishes.id and ratings.status=1')
				->leftJoin('restaurants','restaurants.user_id = dishes.user_id')
				->leftJoin('countries as restaurant_country','restaurant_country.id = restaurants.country_id')
				->asArray()
				->where(['dishes.status'=> 1])
				->groupBy(['dishes.id']);
			
			//Sorting based on feed type
			if($feed_type == Yii::$app->params['FEED_TYPE']['ranked']){
				$listDishes = $listDishes
					//->having('count(ratings.id) >= 2')
					->orderBy(['rank' => SORT_DESC]);
			}else{
				$listDishes = $listDishes->orderBy('RAND('.$random_offset.')');
			}
			
			//filter posts
			if($location_type == Yii::$app->params['LOCATION_TYPE']['national']){
				$listDishes->andWhere(['restaurant_country.code' => $country_code]);
			}else{
				$listDishes = $listDishes->andwhere('(((acos(sin(('.$latitude.'*pi()/180)) * sin((restaurants.restaurant_lat*pi()/180))+cos(('.$latitude.'*pi()/180)) * cos((restaurants.restaurant_lat*pi()/180)) * cos((('.$longitude.'- restaurants.restaurant_long)*pi()/180))))*180/pi())*60*1.1515) <= '.Yii::$app->params['LOCAL_DISTANCE']);
			}
			
			if(!empty($cuisine_id)){
				$listDishes = $listDishes->andwhere(['dishes.cuisine_id' => $cuisine_id]);
			}
			
			if(!empty($meal_id)){
				$listDishes = $listDishes->andwhere(['dishes.meal_id' => array_filter($meal_id)]);
			}
			
			if(!empty($quick_food_id)){
				$listDishes = $listDishes->andwhere(['dishes.quick_food_id' => array_filter($quick_food_id)]);
			}
			
			if(!empty($search)){
				$listDishes = $listDishes->andwhere(
					[
						'or',
						['like', 'dishes.title', $search],
						['like', 'restaurants.name', $search]
				 	]);
			}
			//Total dishes count
			$allDishesCount = $listDishes->count();
					
			$listDishes = $listDishes
							->offset($current_index * $per_page)
							->limit($per_page)
							->all();
						
			if (!empty($listDishes))
			{
				$response = [];
				foreach($listDishes as $dishDetails){
					$dishResponse = [
						'dish_id' => $dishDetails['id'],
						'title' => $dishDetails['title'],
						'description' => $dishDetails['description'],
						'currency_symbol' => $dishDetails['currency_symbol'],
						'currency_code' => $dishDetails['currency_code'],
						'price' => $dishDetails['price'],
						'avg_dish_rating' => $dishDetails['avg_dish_rating'],
						'avg_quality_rating' => $dishDetails['avg_quality_rating'],
						'avg_appearance_rating' => $dishDetails['avg_appearance_rating'],
						'total_ratings' => $dishDetails['total_ratings'],
						'user' => $dishDetails['user'],
						'restaurant_details' => [
							'name' => $dishDetails['name'],
							'mobile' => $dishDetails['mobile'],
							'address' => $dishDetails['address'],
							'restaurant_lat' => $dishDetails['restaurant_lat'],
							'restaurant_long' => $dishDetails['restaurant_long']
						],
						'cuisine' => [
							'id' => $dishDetails['cuisine']['id'],
							'name' => $dishDetails['cuisine']['name'],
							'image' => $dishDetails['cuisine']['image']
						],
						'meal' => [
							'id' => $dishDetails['meal']['id'],
							'name' => $dishDetails['meal']['name'],
							'image' => $dishDetails['meal']['image']
						]
					];
					if(!empty($dishDetails['quickFood'])){
						$dishResponse['quickFood'] = [
							'id' => $dishDetails['quickFood']['id'],
							'name' => $dishDetails['quickFood']['name'],
							'image' => $dishDetails['quickFood']['image']
						];
					}
					if(!empty($dishDetails['userRating'])){
						$dishResponse['userRating'] = [
							'rating_id' => $dishDetails['userRating']['id'],
							'dish_rating' => $dishDetails['userRating']['dish_rating'],
							'quality_rating' => $dishDetails['userRating']['quality_rating'],
							'appearance_rating' => $dishDetails['userRating']['appearance_rating'],
							'feedback' => $dishDetails['userRating']['feedback']
						];
					}
					if(!empty($dishDetails['dishMedia'])){
						$dishMediaResponse = [];
						foreach($dishDetails['dishMedia'] as $dishMediaDetails){
							$dishMediaResponse[] = [
								'type' => $dishMediaDetails['type'],
								'url' => $dishMediaDetails['url'],
								'thumb_url' => $dishMediaDetails['thumb_url']
							];
						}
						$dishResponse['dishMedia'] = $dishMediaResponse;
					}
					$response[] = $dishResponse;
				}
				//total number of pages
				$extra = ['total_number_of_pages'=>  ceil($allDishesCount/$per_page),'random_offset' => $random_offset];
				return CommonApiHelper::return_success_response("","",$response,$extra);
			}else{
				return CommonApiHelper::return_error_response('No dish found.','','2');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user to dish details
     */
    public function actionDishDetails()
    {
        //validate webservice
        $requiredParams = ['dish_id'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			$user_id = isset($post['user_id']) ? $post['user_id'] : '';
			$dish_id = $post['dish_id'];
			
			//Fetch related details			
			if(!empty($user_id)){
				$relatedDetails = ['cuisine','meal','quickFood','dishMedia',
								   'userRating' =>  function($query)use($user_id) {
										$query->where(['user_id' => $user_id,'status'=>1]);
								 	},'user' =>  function($query) {
										$query->select(['user_id','name','profile_pic']);
								 	}
								  ];
			}else{
				$relatedDetails = ['cuisine','meal','quickFood','dishMedia','user' =>  function($query) {
										$query->select(['user_id','name','profile_pic']);
								 	}];
			}
			
			//Find dish details
			$dishDetails = Dishes::find()
				->select([
					'IFNULL((((avg(ratings.dish_rating)/8)*(60/100)) + ((avg(ratings.quality_rating)/4)*(25/100)) + ((avg(ratings.appearance_rating)/4)*(15/100))),0) as rank',
					'dishes.id','dishes.user_id','dishes.cuisine_id','dishes.meal_id',
					'dishes.quick_food_id','dishes.title','dishes.description','dishes.price',
					'IFNULL(avg(ratings.dish_rating),0) as avg_dish_rating','IFNULL(avg(ratings.quality_rating),0) as avg_quality_rating','IFNULL(avg(ratings.appearance_rating),0) as avg_appearance_rating','count(ratings.id) as total_ratings',
					'restaurants.name','restaurants.mobile','restaurants.address','restaurants.restaurant_lat','restaurants.restaurant_long',
					'restaurant_country.currency_symbol','restaurant_country.currency_code'
				])
				->with($relatedDetails)
				->leftJoin('ratings','ratings.dish_id = dishes.id and ratings.status=1')
				->leftJoin('restaurants','restaurants.user_id = dishes.user_id')
				->leftJoin('countries as restaurant_country','restaurant_country.id = restaurants.country_id')
				->asArray()
				->where(['dishes.id'=> $dish_id,'dishes.status'=> 1])
				->groupBy(['dishes.id'])
				->one();
						
			if (!empty($dishDetails))
			{
				$dishResponse = [
					'dish_id' => $dishDetails['id'],
					'title' => $dishDetails['title'],
					'description' => $dishDetails['description'],
					'currency_symbol' => $dishDetails['currency_symbol'],
					'currency_code' => $dishDetails['currency_code'],
					'price' => $dishDetails['price'],
					'avg_dish_rating' => $dishDetails['avg_dish_rating'],
					'avg_quality_rating' => $dishDetails['avg_quality_rating'],
					'avg_appearance_rating' => $dishDetails['avg_appearance_rating'],
					'total_ratings' => $dishDetails['total_ratings'],
					'user' => $dishDetails['user'],
					'restaurant_details' => [
						'name' => $dishDetails['name'],
						'mobile' => $dishDetails['mobile'],
						'address' => $dishDetails['address'],
						'restaurant_lat' => $dishDetails['restaurant_lat'],
						'restaurant_long' => $dishDetails['restaurant_long']
					],
					'cuisine' => [
						'id' => $dishDetails['cuisine']['id'],
						'name' => $dishDetails['cuisine']['name'],
						'image' => $dishDetails['cuisine']['image']
					],
					'meal' => [
						'id' => $dishDetails['meal']['id'],
						'name' => $dishDetails['meal']['name'],
						'image' => $dishDetails['meal']['image']
					]
				];
				if(!empty($dishDetails['quickFood'])){
					$dishResponse['quickFood'] = [
						'id' => $dishDetails['quickFood']['id'],
						'name' => $dishDetails['quickFood']['name'],
						'image' => $dishDetails['quickFood']['image']
					];
				}
				if(!empty($dishDetails['userRating'])){
					$dishResponse['userRating'] = [
						'rating_id' => $dishDetails['userRating']['id'],
						'dish_rating' => $dishDetails['userRating']['dish_rating'],
						'quality_rating' => $dishDetails['userRating']['quality_rating'],
						'appearance_rating' => $dishDetails['userRating']['appearance_rating'],
						'feedback' => $dishDetails['userRating']['feedback']
					];
				}
				if(!empty($dishDetails['dishMedia'])){
					$dishMediaResponse = [];
					foreach($dishDetails['dishMedia'] as $dishMediaDetails){
						$dishMediaResponse[] = [
							'type' => $dishMediaDetails['type'],
							'url' => $dishMediaDetails['url'],
							'thumb_url' => $dishMediaDetails['thumb_url']
						];
					}
					$dishResponse['dishMedia'] = $dishMediaResponse;
				}
				$response[] = $dishResponse;
				return CommonApiHelper::return_success_response("","",$response);
			}else{
				return CommonApiHelper::return_error_response('No dish found.','','2');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user to delete dish
     */
    public function actionDeleteDish()
    {
		//validate webservice
        $requiredParams = ['user_id','dish_id'];        
        CommonApiHelper::validateRequestParameters($requiredParams);        
        $response = [];		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);
			$user_id = $post['user_id'];
			$dish_id = $post['dish_id'];
			
			//Check if Dish exist or not
			//If exist then delete
			$dishExist = Dishes::find()->where(['user_id' => $user_id,'id' => $dish_id,'status' => 1])->one();
			if(!empty($dishExist)){				
				$dishExist->status = 0;
				$dishExist->save(false);				
				return CommonApiHelper::return_success_response("Dish has been removed.","",$response);	
			}else{
				return CommonApiHelper::return_error_response('No dish exist.','','2');
			}			
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user to save ratings
     */
    public function actionSaveRating()
    {
		//validate webservice
        $requiredParams = ['user_id','dish_id','dish_rating','quality_rating','appearance_rating'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$user_id = $post['user_id'];
			$dish_id = $post['dish_id'];
			$dish_rating = $post['dish_rating'];
			$quality_rating = $post['quality_rating'];
			$appearance_rating = $post['appearance_rating'];
			$feedback = !empty($post['feedback']) ? $post['feedback'] : null ;
			
			//Save rating
			//Check if Rating exist or not
			//If exist then pass save else update
			$ratings = Ratings::find()->where(['user_id' => $user_id,'dish_id' => $dish_id])->one();
			if(empty($ratings)){
				$ratings               = new Ratings();
				$ratings->user_id      = $user_id;
				$ratings->dish_id        = $dish_id;
			}
			$ratings->dish_rating   = $dish_rating;
			$ratings->quality_rating      = $quality_rating;
			$ratings->appearance_rating        = $appearance_rating;
			$ratings->feedback  = $feedback;
			$ratings->status        = 1;
			if($ratings->save(false)){
				$response[] = [
					'rating_id' => $ratings->id,
					'dish_rating' => $ratings->dish_rating,
					'quality_rating' => $ratings->quality_rating,
					'appearance_rating' => $ratings->appearance_rating,
					'feedback' => $ratings->feedback
				];
				
				//Fetch dish details
				$dishDetails = Dishes::findOne(['id' => $post['dish_id']]);
				
				//Send push notification to restaurant owner, if it is not rated by itself
				if($dishDetails->user_id != $user_id){
					//send push notification to restaurant user
					$restaurantUserDeviceTokens = UsersAccessTokens::find()->where(['user_id' => $dishDetails->user_id])->all();

					//Fetch user details
					$loggedUserDetails = Users::findOne(['user_id' => $user_id]);

					$androidAccessTokens = $iosAccessTokens = [];
					foreach($restaurantUserDeviceTokens as $tokenDetails){
						if(!empty(trim($tokenDetails->device_token))){
							if($tokenDetails->device_type == Yii::$app->params['DEVICE_TYPE']['android']){
								$androidAccessTokens[] = $tokenDetails->device_token;
							}elseif($tokenDetails->device_type == Yii::$app->params['DEVICE_TYPE']['ios']){
								$iosAccessTokens[] = $tokenDetails->device_token;
							}
						}
					}
					if(!empty($androidAccessTokens)){
						//send push notification to android
						$pushOptions = [
							'device_token' => $androidAccessTokens,
							'message' => $loggedUserDetails->name.' has added review on your dish',
							'dish_id' => $post['dish_id']
						];
						CommonApiHelper::sendFCMPushnotification($pushOptions,'1');
					}
					if(!empty($iosAccessTokens)){
						//send push notification to ios
						$pushOptions = [
							'device_token' => $iosAccessTokens,
							'message' => $loggedUserDetails->name.' has added review on your dish',
							'dish_id' => $post['dish_id']
						];
						CommonApiHelper::sendFCMPushnotification($pushOptions,'2');
					}
				}
				
				return CommonApiHelper::return_success_response("Rating has been saved.","",$response);	
			}else{
				return CommonApiHelper::return_error_response('Sorry, Please try again.');
			}			
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	/**
     * this function used by the application user to delete rating
     */
    public function actionDeleteRating()
    {
		//validate webservice
        $requiredParams = ['user_id','dish_id'];        
        CommonApiHelper::validateRequestParameters($requiredParams);        
        $response = [];		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);
			$user_id = $post['user_id'];
			$dish_id = $post['dish_id'];
			
			//Save rating
			//Check if Rating exist or not
			//If exist then delete
			$ratings = Ratings::find()->where(['user_id' => $user_id,'dish_id' => $dish_id,'status' => 1])->one();
			if(!empty($ratings)){				
				$ratings->status = 0;
				$ratings->save(false);				
				return CommonApiHelper::return_success_response("Rating has been removed.","",$response);	
			}else{
				return CommonApiHelper::return_error_response('No rating exist.','','2');
			}			
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	
	/**
     * this function used by the application user to list ratings
     */
    public function actionListRatings()
    {
        //validate webservice
        $requiredParams = ['dish_id','page'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			$dish_id = $post['dish_id'];
			$current_index = $post['page'];
			
			//Records per page
			$per_page = Yii::$app->params['records_per_page'];
			
			//Find list of dishes
			$listRatings = Ratings::find()
				->select(['ratings.id as rating_id','ratings.user_id','ratings.dish_rating','ratings.quality_rating','ratings.appearance_rating','ratings.feedback','ratings.updated_at as datetime'])
				->with(['user' =>  function($query) {
									$query->select(['user_id','user_type','name','email','profile_pic']);
								}
					   ])
				->asArray()
				->where(['ratings.status'=> 1,'ratings.dish_id'=> $dish_id])
				->orderBy(['ratings.id' => SORT_DESC]);
			
			//Total dishes count
			$allRatingsCount = $listRatings->count();
					
			$listRatings = $listRatings
							->offset($current_index * $per_page)
							->limit($per_page)
							->all();
		
			if (!empty($listRatings))
			{
				//total number of pages
				$extra = ['total_number_of_pages'=>  ceil($allRatingsCount/$per_page)];
				return CommonApiHelper::return_success_response("","",$listRatings,$extra);
			}else{
				return CommonApiHelper::return_error_response('No rating found.','','2');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	
	/**
     * this function used by the application user for my dishes and my reviews
     */
    public function actionProfileDishes()
    {
        //validate webservice
        $requiredParams = ['user_id','type','page'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			$user_id = isset($post['user_id']) ? $post['user_id'] : '';
			$type = $post['type'];
			$current_index = $post['page'];
			
			//Records per page
			$per_page = Yii::$app->params['records_per_page'];
			
			//Fetch related details			
			$relatedDetails = ['cuisine','meal','quickFood','dishMedia',
								   	'userRating' =>  function($query)use($user_id) {
										$query->where(['user_id' => $user_id,'status'=>1]);
									},'user' =>  function($query) {
										$query->select(['user_id','name','profile_pic']);
									}
							  	];
			
			//Find list of dishes
			$listDishes = Dishes::find()
				->select([
					'IFNULL((((avg(ratings.dish_rating)/8)*(60/100)) + ((avg(ratings.quality_rating)/4)*(25/100)) + ((avg(ratings.appearance_rating)/4)*(15/100))),0) as rank',
					'dishes.id','dishes.user_id','dishes.cuisine_id','dishes.meal_id',
					'dishes.quick_food_id','dishes.title','dishes.description','dishes.price',
					'IFNULL(avg(ratings.dish_rating),0) as avg_dish_rating','IFNULL(avg(ratings.quality_rating),0) as avg_quality_rating','IFNULL(avg(ratings.appearance_rating),0) as avg_appearance_rating','count(ratings.id) as total_ratings',
					'restaurants.name','restaurants.mobile','restaurants.address','restaurants.restaurant_lat','restaurants.restaurant_long',
					'restaurant_country.currency_symbol','restaurant_country.currency_code'
				])
				->with($relatedDetails)
				->leftJoin('ratings','ratings.dish_id = dishes.id and ratings.status=1')
				->leftJoin('restaurants','restaurants.user_id = dishes.user_id')
				->leftJoin('countries as restaurant_country','restaurant_country.id = restaurants.country_id')
				->asArray()
				->where(['dishes.status'=> 1])
				->groupBy(['dishes.id']);
						
			//condition for my dishes
			if($type == 1){
				$listDishes = $listDishes->orderBy(['dishes.created_at' => SORT_DESC]);
				$listDishes->andWhere(['dishes.user_id' => $user_id]);
			//condition for my review
			}else{
				$listDishes = $listDishes->orderBy(['ratings.created_at' => SORT_DESC]);
				$listDishes->andWhere(['ratings.user_id' => $user_id]);
			}
			
			//Total dishes count
			$allDishesCount = $listDishes->count();
					
			$listDishes = $listDishes
							->offset($current_index * $per_page)
							->limit($per_page)
							->all();
						
			if (!empty($listDishes))
			{
				$response = [];
				foreach($listDishes as $dishDetails){
					$dishResponse = [
						'dish_id' => $dishDetails['id'],
						'title' => $dishDetails['title'],
						'description' => $dishDetails['description'],
						'currency_symbol' => $dishDetails['currency_symbol'],
						'currency_code' => $dishDetails['currency_code'],
						'price' => $dishDetails['price'],
						'avg_dish_rating' => $dishDetails['avg_dish_rating'],
						'avg_quality_rating' => $dishDetails['avg_quality_rating'],
						'avg_appearance_rating' => $dishDetails['avg_appearance_rating'],
						'total_ratings' => $dishDetails['total_ratings'],
						'user' => $dishDetails['user'],
						'restaurant_details' => [
							'name' => $dishDetails['name'],
							'mobile' => $dishDetails['mobile'],
							'address' => $dishDetails['address'],
							'restaurant_lat' => $dishDetails['restaurant_lat'],
							'restaurant_long' => $dishDetails['restaurant_long']
						],
						'cuisine' => [
							'id' => $dishDetails['cuisine']['id'],
							'name' => $dishDetails['cuisine']['name'],
							'image' => $dishDetails['cuisine']['image']
						],
						'meal' => [
							'id' => $dishDetails['meal']['id'],
							'name' => $dishDetails['meal']['name'],
							'image' => $dishDetails['meal']['image']
						]
					];
					if(!empty($dishDetails['quickFood'])){
						$dishResponse['quickFood'] = [
							'id' => $dishDetails['quickFood']['id'],
							'name' => $dishDetails['quickFood']['name'],
							'image' => $dishDetails['quickFood']['image']
						];
					}
					if(!empty($dishDetails['userRating'])){
						$dishResponse['userRating'] = [
							'rating_id' => $dishDetails['userRating']['id'],
							'dish_rating' => $dishDetails['userRating']['dish_rating'],
							'quality_rating' => $dishDetails['userRating']['quality_rating'],
							'appearance_rating' => $dishDetails['userRating']['appearance_rating'],
							'feedback' => $dishDetails['userRating']['feedback']
						];
					}
					if(!empty($dishDetails['dishMedia'])){
						$dishMediaResponse = [];
						foreach($dishDetails['dishMedia'] as $dishMediaDetails){
							$dishMediaResponse[] = [
								'type' => $dishMediaDetails['type'],
								'url' => $dishMediaDetails['url'],
								'thumb_url' => $dishMediaDetails['thumb_url']
							];
						}
						$dishResponse['dishMedia'] = $dishMediaResponse;
					}
					$response[] = $dishResponse;
				}
				//total number of pages
				$extra = ['total_number_of_pages'=>  ceil($allDishesCount/$per_page),'total_number_of_dishes' => $allDishesCount];
				return CommonApiHelper::return_success_response("","",$response,$extra);
			}else{
				return CommonApiHelper::return_error_response('No dish found.','','2');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
}