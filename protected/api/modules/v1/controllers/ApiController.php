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
use common\models\Countries;
use FFMpeg;


/**
 * Simple controller, all response are JSON type.
 */
class ApiController extends Controller
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
	
	public function actionEmailTest(){
		//send email to new registered user
		Yii::$app->mailer->compose()
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
			->setTo("er.rhpatel@gmail.com")
			->setSubject("test subject")
			->setHtmlBody("test body")
			->send();
		return CommonApiHelper::return_success_response("success", "success",[]);
    }
	
	public function actionCreateThumbnail(){
		//require 'vendor/autoload.php';

		$sec = 3;
		$movie = Yii::$app->params['DOCUMENT_ROOT'].'uploads/dishes/video.mp4';
		$thumbnail = Yii::$app->params['DOCUMENT_ROOT'].'uploads/dishes/thumbnail.png';

		$ffmpeg = FFMpeg\FFMpeg::create();
		$video = $ffmpeg->open($movie);
		$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec));
		$frame->save($thumbnail);
		//echo '<img src="'.$thumbnail.'">';
    }
	
    /**
     * this function used by the application user for email unique check.
     */
    public function actionUniqueEmailCheck()
    {
        //validate webservice
        $requiredParams = ['email'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];

        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);
        $email = $post['email'];
        
        //Email uniqueness check
        $userEmailCheck = Users::find()->where(['email'=>$email])->asArray()->one();
        if (empty($userEmailCheck)) {            
			return CommonApiHelper::generate_success_response("Email Id is available",[]);
        } else {
            return CommonApiHelper::generate_error_response('3', 'Email Id has been already registered. Please try with different email Id.');
        }
    }
        
    /**
     * this function used by the application user for signUp purpose.
     */
    public function actionSignUp()
    {
		//validate webservice
        $requiredParams = ['name','email','password','device_type'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$name = $post['name'];
			$email = $post['email'];
			$password = $post['password'];
			$device_type = $post['device_type'];
						
			if (isset($post['email']) && !empty($post['email']))
			{
				$emailExist = Users::findOne(['email' => $post['email']]);
				if (isset($emailExist) && !empty($emailExist))
				{
					return CommonApiHelper::return_error_response("Email already registered:", "Please try with different email","2");
				}
			}

			$modelUser               = new Users();
			$modelUser->role_id      = 2;
			$modelUser->user_type    = Yii::$app->params['USER_TYPE']['user'];
			$modelUser->email        = !empty($post['email']) ? trim($post['email']) : '';
			$modelUser->name         = !empty($post['name']) ? trim($post['name']) : '';
			$modelUser->profile_pic  = !empty($post['profile_pic']) ? trim($post['profile_pic']) : '';
			$modelUser->password     = !empty($post['password']) ? password_hash($post['password'], PASSWORD_BCRYPT) : null;
			$modelUser->status       = 1;
			
			if ($modelUser->save(false))
			{
				// Update user's device token and acccess token 
				$access_token = md5($modelUser->user_id.time());			
				$userAccessToken = new UsersAccessTokens;                
				$userAccessToken->user_id = $modelUser->user_id;
				$userAccessToken->access_token = $access_token;
				$userAccessToken->device_token = !empty($post['device_token']) ? trim($post['device_token']) : '';
				$userAccessToken->device_type = !empty($device_type) ? $device_type : Yii::$app->params['DEVICE_TYPE']['android'];
				$userAccessToken->save(false);
				
				/*//save the profile picture.
				if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name']))
				{      
					$modelUser->profile_pic = time().$_FILES['profile_pic']['name'];
					move_uploaded_file($_FILES['profile_pic']['tmp_name'],Yii::$app->params['DOCUMENT_ROOT'].'uploads/profile_pic/'.$modelUser->profile_pic);	
				}
				else
				{
					$modelUser->profile_pic = "";
				}
				$modelUser->save(false);*/
			}
		
			//Prepare response
			$response[] 	= 	[
				'user_id'			=>	$modelUser->user_id,
				'user_type'			=>	$modelUser->user_type,
				'name'				=>	$modelUser->name,
				'email'				=>	$modelUser->email,
				'profile_pic'		=>	!empty($modelUser->profile_pic) ? $modelUser->profile_pic : '',
				'is_otp_verified' 	=> 	$modelUser->is_otp_verified,
				'access_token'		=>	$userAccessToken->access_token
			];
					
			if (!empty($modelUser->email)) {
				$emailformatemodel = EmailFormat::findOne(["id" => Yii::$app->params['EMAIL_TEMPLATE_ID']['welcome'], "status" => '1']);
				if ($emailformatemodel)
				{
					//create email body
					$AreplaceString = array('{password}' => $post['password'], '{name}' => $modelUser->name, '{email}' => $modelUser->email);
					$body       = CommonApiHelper::MailTemplate($AreplaceString, $emailformatemodel->body);
					$ssSubject  = $emailformatemodel->subject;
					//send email to new registered user
					Yii::$app->mailer->compose()
						->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
						->setTo($modelUser->email)
						->setSubject($ssSubject)
						->setHtmlBody($body)
						->send(); 
				}
			}
			
			return CommonApiHelper::return_success_response("Sign Up Successfully.","",$response);	
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}
    }

	/**
     * this function used by the application user for restaurant otp verification.
     */
    public function actionRestaurantOtpVerification()
    {
		//validate webservice
        $requiredParams = ['user_id','otp'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$user_id = $post['user_id'];
			//$access_token = $post['access_token'];
			$otp = $post['otp'];
			
			//Fetch logged in user details
			$userdata = Users::find()->where(['user_id' => $user_id])->one();
			
			//Verify OTP
			$otpVerified = RestaurantOtp::findOne(['email' => $userdata->email,'status' => 1]);
			if(empty($otpVerified)){
				return CommonApiHelper::return_error_response("OTP does not exist:","Please contact Administrator to Generate OTP for your Restaurant.","2");
			}else if($otpVerified->restaurant_otp != $otp){
				return CommonApiHelper::return_error_response("Invalid OTP:","Please enter correct OTP.","3");
			}
			
			//Update User Type to Restaurant and Verify OTP
			$userdata->user_type    = Yii::$app->params['USER_TYPE']['restaurant'];
			$userdata->is_otp_verified       = 1;
			$userdata->save(false);
			
			//Prepare response
			$response[] 	= 	[
				'user_id'			=>	$userdata->user_id,
				'user_type'			=>	$userdata->user_type,
				'name'				=>	$userdata->name,
				'email'				=>	$userdata->email,
				'profile_pic'		=>	!empty($userdata->profile_pic) ? $userdata->profile_pic : '',
				'is_otp_verified' 	=> 	$userdata->is_otp_verified
			];
			//Remove OTP
			$otpVerified->delete();
		
			return CommonApiHelper::return_success_response("OTP has been verified successfully.","",$response);	
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
	
	/**
     * this function used by the application user for add restaurant details
     */
    public function actionSaveRestaurantDetails()
    {
		//validate webservice
        $requiredParams = ['user_id','name','restaurant_lat','restaurant_long'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
        
        $response = [];
		
		try{
			//Get request parameters.
			$post = Yii::$app->request->bodyParams;
			$post = array_map('trim', $post);

			$user_id = $post['user_id'];
			$name = $post['name'];
			$mobile = !empty($post['mobile']) ? $post['mobile'] : null ;
			$address = !empty($post['address']) ? $post['address'] : null ;
			$country_code = !empty($post['country_code']) ? $post['country_code'] : 'US' ;
			$restaurant_google_id = !empty($post['restaurant_google_id']) ? $post['restaurant_google_id'] : null;			
			$restaurant_lat = $post['restaurant_lat'];
			$restaurant_long = $post['restaurant_long'];
			
			//Fetch country details
			$countryDetails = Countries::findOne(["code"=>$country_code]);
			
			//Fetch logged in user details
			$userdata = Users::find()->where(['user_id' => $user_id])->one();
			
			//update user type to restaurant user
			$userdata->user_type    = Yii::$app->params['USER_TYPE']['restaurant'];
			$userdata->save(false);
			
			//Check if Restaurannt exist or not
			//If exist then Update Restaurant details
			//If not exist then Add Restaurant details
			$restaurants = Restaurants::find()->where(['user_id' => $user_id])->one();
			if(empty($restaurants)){
				$restaurants               = new Restaurants();
			}
			$restaurants->user_id         = $user_id;
			$restaurants->name         = !empty($post['name']) ? trim($post['name']) : '';
			$restaurants->restaurant_google_id         = !empty($post['restaurant_google_id']) ? trim($post['restaurant_google_id']) : '';
			$restaurants->country_id         = $countryDetails->id;			
			$restaurants->mobile        = !empty($post['mobile']) ? trim($post['mobile']) : '';
			$restaurants->address        = !empty($post['address']) ? trim($post['address']) : '';
			$restaurants->restaurant_lat        = !empty($post['restaurant_lat']) ? trim($post['restaurant_lat']) : '';
			$restaurants->restaurant_long        = !empty($post['restaurant_long']) ? trim($post['restaurant_long']) : '';
			$restaurants->save(false);
			
			//send email to owner about restaurant registration
			if (!empty(Yii::$app->params['SITE_OWNER_EMAIL'])) {
				$emailformatemodel = EmailFormat::findOne(["id" => Yii::$app->params['EMAIL_TEMPLATE_ID']['restaurant_registered'], "status" => '1']);
				if ($emailformatemodel)
				{
					//create email body
					$AreplaceString = array(
						'{site_owner_name}' => Yii::$app->params['SITE_OWNER_NAME'], 
						'{restaurant_name}' => $restaurants->name, 
						'{restaurant_owner_name}' => $userdata->name,
						'{phone_number}' => $restaurants->mobile,
						'{email}' => $userdata->email,
						'{address}' => $restaurants->address
					);
					$body       = CommonApiHelper::MailTemplate($AreplaceString, $emailformatemodel->body);
					$ssSubject  = $emailformatemodel->subject;
					//send email to new registered user
					Yii::$app->mailer->compose()
						->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
						->setTo(Yii::$app->params['SITE_OWNER_EMAIL'])
						->setSubject($ssSubject)
						->setHtmlBody($body)
						->send(); 
				}
			}
			
			//Prepare response
			$restaurantDetails = [
				'name' => $restaurants->name,
				'restaurant_google_id' => $restaurants->restaurant_google_id,
				'mobile' => $restaurants->mobile,
				'address' => $restaurants->address,
				'restaurant_lat' => $restaurants->restaurant_lat,
				'restaurant_long' => $restaurants->restaurant_long
			];
			$response[] 	= 	[
				'user_id'			=>	$userdata->user_id,
				'user_type'			=>	$userdata->user_type,
				'name'				=>	$userdata->name,
				'email'				=>	$userdata->email,
				'profile_pic'		=>	!empty($userdata->profile_pic) ? $userdata->profile_pic : '',
				'is_otp_verified' 	=> 	$userdata->is_otp_verified,
				'restaurant_details'=> $restaurantDetails
			];
		
			return CommonApiHelper::return_success_response("Restaurant details has been saved.","",$response);	
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
	
    /**
     * this function used by the application user for signUp purpose.
     */
    public function actionLogin()
    {
		//validate webservice
        $requiredParams = ['email','password','device_type'];
        
        CommonApiHelper::validateRequestParameters($requiredParams);
		
        $response = [];
        $post     = Yii::$app->request->bodyParams;
        $post     = array_map('trim', $post);
		$device_token = !empty($post['device_token']) ? $post['device_token'] : '';
		$device_type = !empty($post['device_type']) ? $post['device_type'] : '';
		
		try{
			//Fetch user details
			$userdata = Users::find()->where(['email' => $post['email']])->one();

			if (!empty($userdata))
			{
				if($userdata->status==1){
					if (!empty($post['password']) && password_verify($post['password'],$userdata->password))
					{
						//Save/update access token of user
						$user_access_token = md5($userdata->user_id.time());
						$access_token = UsersAccessTokens::find()->where(['user_id'=>$userdata->user_id,'device_token'=>$device_token])->one();
						if(empty($access_token)){
							$access_token = new UsersAccessTokens();
							$access_token->user_id = $userdata->user_id;
							$access_token->device_type = !empty($device_type) ? $device_type : Yii::$app->params['DEVICE_TYPE']['android'];
							$access_token->device_token = !empty($post['device_token'])?$post['device_token']:'';
						}                        
						$access_token->access_token = $user_access_token;
						$access_token->save(false);
						
						$data 	= 	[
							'user_id'			=>	$userdata->user_id,
							'user_type'			=>	$userdata->user_type,
							'name'				=>	$userdata->name,
							'email'				=>	$userdata->email,
							'profile_pic'		=>	!empty($userdata->profile_pic) ? $userdata->profile_pic : '',
							'is_otp_verified' 	=> 	$userdata->is_otp_verified,
							'access_token'		=>	$user_access_token
						];
						
						/*//Check if Restaurannt exist or not
						//If exist then pass Restaurant details in response
						$restaurants = Restaurants::find()->where(['user_id' => $userdata->user_id])->one();
						$restaurantDetails = [];
						if(!empty($restaurants)){
							//Prepare response
							$restaurantDetails = [
								'name' => $restaurants->name,
								'restaurant_google_id' => $restaurants->restaurant_google_id,
								'mobile' => $restaurants->mobile,
								'address' => $restaurants->address,
								'restaurant_lat' => $restaurants->restaurant_lat,
								'restaurant_long' => $restaurants->restaurant_long
							];
							$data['restaurant_details'] = $restaurantDetails;
						}*/
						$response[] = $data;
						return CommonApiHelper::return_success_response("Login successfully.","",$response);	
					}else{
						return CommonApiHelper::return_error_response("Login failed:", "Please enter correct email or password","2");
					}
				}else{
					return CommonApiHelper::return_error_response("Login failed:", "Your account is Inactive. Please contact Administrator for more details.","3");
				}            
			}else{
				return CommonApiHelper::return_error_response("Login failed:", "Your are not registered, please signup.","4");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}
    }
    
    /**
     * this function used by the application user for logout purpose.
     */
    public function actionLogout()
    {
        //validate webservice
        $requiredParams = ['user_id'];

        CommonApiHelper::validateRequestParameters($requiredParams);
		
        try{
			$post     = Yii::$app->request->bodyParams;
			$post     = array_map('trim', $post);
			$user_id  = $post['user_id'];
			$access_token = Yii::$app->request->headers->get('access_token');
			
			//Find user details.
			$modelUser = Users::findOne(['user_id'=>$user_id]);
			
			//Find user access token
			$UsersAccessToken = UsersAccessTokens::findOne(['user_id'=>$user_id,'access_token'=>$access_token]);
			
			//Make user access_token, device_type, device_token make empty to make user logout.
			if(!empty($modelUser)){
				//Remove user access token
				if(!empty($UsersAccessToken)){         
					$UsersAccessToken->delete();
				}
				return CommonApiHelper::return_success_response("You have succefully logged out.","",[]);	
			}else{
				return CommonApiHelper::return_error_response('Sorry, Please try again.');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    }
    
    /**
     * this function used by the application user for User profile details.
	 */
     
    public function actionUserProfile()
    {
        //validate webservice
        $requiredParams = ['user_id'];
        CommonApiHelper::validateRequestParameters($requiredParams);

        try{
			$post     = Yii::$app->request->bodyParams;
			$post     = array_map('trim', $post);
			$user_id  = $post['user_id'];
			
			//Fetch user details
			$userdata = Users::find()->where(['user_id' => $user_id])->one();

			if (!empty($userdata))
			{				
				$data 	= 	[
					'user_id'			=>	$userdata->user_id,
					'user_type'			=>	$userdata->user_type,
					'name'				=>	$userdata->name,
					'email'				=>	$userdata->email,
					'profile_pic'		=>	!empty($userdata->profile_pic) ? $userdata->profile_pic : ''
				];

				//Check if Restaurannt exist or not
				//If exist then pass Restaurant details in response
				$restaurants = Restaurants::find()->where(['user_id' => $userdata->user_id])->one();
				$restaurantDetails = [];
				if(!empty($restaurants)){
					//Prepare response
					$restaurantDetails = [
						'name' => $restaurants->name,
						'restaurant_google_id' => $restaurants->restaurant_google_id,
						'mobile' => $restaurants->mobile,
						'address' => $restaurants->address,
						'restaurant_lat' => $restaurants->restaurant_lat,
						'restaurant_long' => $restaurants->restaurant_long
					];
					$data['restaurant_details'] = $restaurantDetails;
				}
				$response[] = $data;
				return CommonApiHelper::return_success_response("","",$response);	
			}else{
				return CommonApiHelper::return_error_response("Sorry, Please try again.");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}
    }
	
    /**
     * this function used by the application user for remove profile photos.
     
    public function actionRemoveProfilePhoto()
    {
        //validate webservice
        $requiredParams = ['user_id'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			
			//Get request parameters
			$user_id = $post['user_id'];
				 
			//Find user details.
			$modelUser = Users::findOne(['user_id'=>$user_id]);
			
			$modelUser->profile_pic = '';
			$message = "Profile photo has been removed.";
		
			if($modelUser->save(false)){
				return CommonApiHelper::generate_success_response($message,[]);
			}else{
				return CommonApiHelper::generate_error_response('1', 'Sorry, Please try again..');
			}
		}catch(\Exception $e){
			return CommonApiHelper::generate_error_response('1', 'Sorry, Please try again.');
		}
    }*/
    
    /**
     * this function used by the application user for Edit profile.
     */
    public function actionEditProfile()
    {
        //validate webservice
        $requiredParams = ['user_id'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);

			$response = [];

			//Get request parameters.
			$request_params = Yii::$app->request->bodyParams;
			$post = array_map('trim', $request_params);
			
			//Get request parameters
			$user_id = $post['user_id'];
			
			//Find user details.
			$modelUser = Users::findOne(['user_id'=>$user_id]);
			
			if (isset($post['email']) && !empty($post['email']))
			{
				$emailExist = Users::find()->where(['email' => $post['email']])->andWhere(['<>',"user_id",$user_id])->one();
				if (isset($emailExist) && !empty($emailExist))
				{
					return CommonApiHelper::return_error_response("Email already registered:", "Please try with different email","2");
				}
			}
			
			$modelUser->email        = isset($post['email']) ? trim($post['email']) : $modelUser->email;
			$modelUser->name         = isset($post['name']) ? trim($post['name']) : $modelUser->name;
			$modelUser->profile_pic  = isset($post['profile_pic']) ? trim($post['profile_pic']) : $modelUser->profile_pic;
					
			$modelUser->save(false);
			//Normal user response
			$data 	= 	[
				'user_id'			=>	$modelUser->user_id,
				'user_type'			=>	$modelUser->user_type,
				'name'				=>	$modelUser->name,
				'email'				=>	$modelUser->email,
				'profile_pic'		=>	!empty($modelUser->profile_pic) ? $modelUser->profile_pic : ''
			];
			//Check if Restaurannt exist or not
			//If exist then update Restaurant details
			$restaurants = Restaurants::find()->where(['user_id' => $user_id])->one();
			$restaurantDetails = [];
			if(!empty($restaurants)){
				if(isset($post['country_code'])){
					//Fetch country details
					$countryDetails = Countries::findOne(["code"=>$post['country_code']]);
				}
				
				$restaurants->name        = isset($post['restaurant_name']) ? trim($post['restaurant_name']) : $restaurants->name;
				$restaurants->country_id         = isset($countryDetails) && !empty($countryDetails) ? $countryDetails->id : $restaurants->country_id;
				$restaurants->mobile        = isset($post['restaurant_mobile']) ? trim($post['restaurant_mobile']) : $restaurants->mobile;
				$restaurants->address        = isset($post['restaurant_address']) ? trim($post['restaurant_address']) : $restaurants->address;
				$restaurants->restaurant_lat        = isset($post['restaurant_lat']) ? trim($post['restaurant_lat']) : $restaurants->restaurant_lat;
				$restaurants->restaurant_long        = isset($post['restaurant_long']) ? trim($post['restaurant_long']) : $restaurants->restaurant_long;
				$restaurants->save(false);
				
				//Prepare response
				$restaurantDetails = [
					'name' => $restaurants->name,
					'restaurant_google_id' => $restaurants->restaurant_google_id,
					'mobile' => $restaurants->mobile,
					'address' => $restaurants->address,
					'restaurant_lat' => $restaurants->restaurant_lat,
					'restaurant_long' => $restaurants->restaurant_long
				];
				$data['restaurant_details'] = $restaurantDetails;
			}
			$response[] = $data;

			return CommonApiHelper::return_success_response("Profile has been updated successfully.","",$response);	
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}
    }
    
    /**
     * This funciton is user for the forgot password api
     *
     * @return     array
    */
    public function actionForgotPassword()
    {
		//validate webservice
        $requiredParams = ['email'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);
			
			$response = [];
			$post     = Yii::$app->request->bodyParams;
			$post     = array_map('trim', $post);
			//Fetch user details
			$userdata = Users::findOne(['email' => $post['email'], 'status' => '1']);
			if (!empty($userdata))
			{
				$userdata->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
				if ($userdata->save(false))
				{
					Users::sendPasswordResetEmail($userdata);
				}
				return CommonApiHelper::return_success_response("Reset Password link has been sent to your registered email id.","",[]);
			}
			else
			{
				return CommonApiHelper::return_error_response("Failed:", "Email id is not registered or User is inactive.","2");
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response("Sorry, Please try again.");
		}
    }

    	 
    /**
     * This funciton is user for the change password
     *
     * @return     array
     */ 
    public function actionChangePassword()
    {
		//validate webservice
        $requiredParams = ['user_id','old_password','new_password'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);
			
			$response = [];
			$post     = Yii::$app->request->bodyParams;
			$post     = array_map('trim', $post);
			
			$response = [];
			$userdata = Users::findOne(['user_id' => $post['user_id'], 'status' => '1']);
			if (!empty($userdata))
			{
				if (password_verify($post['old_password'], $userdata['password']))
				{
					$userdata->password = password_hash($post['new_password'], PASSWORD_BCRYPT);
					$userdata->save(false);

					return CommonApiHelper::return_success_response("Password has been changed successfully.","",[]);
				}
				else
				{
					return CommonApiHelper::return_error_response("Failed:", "Please enter correct old password.","2");
				}
			}
			else
			{
				return CommonApiHelper::return_error_response('Sorry, Please try again.');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    } 
	
    	 
    /**
     * This funciton is user for the update device token
     *
     * @return     array
     */ 
    public function actionUpdateDeviceToken()
    {
		//validate webservice
        $requiredParams = ['user_id','device_type','device_token'];
		try{
			CommonApiHelper::validateRequestParameters($requiredParams);
			
			$response = [];
			$post     = Yii::$app->request->bodyParams;
			$post     = array_map('trim', $post);
			//Fetch access token from HEAD
			$access_token = Yii::$app->request->headers->get('access_token');
			
			$response = [];
			//Fetch existing record
			$accessTokenDetails = UsersAccessTokens::find()->where(['user_id'=>$post['user_id'],'access_token'=>$access_token])->one();
			if (!empty($accessTokenDetails))
			{
				$accessTokenDetails->device_type = !empty($post['device_type']) ? $post['device_type'] : Yii::$app->params['DEVICE_TYPE']['android'];
				$accessTokenDetails->device_token = !empty($post['device_token'])?$post['device_token']:'';
				$accessTokenDetails->save(false);
				return CommonApiHelper::return_success_response("Device token has been updated successfully.","",[]);
			}
			else
			{
				return CommonApiHelper::return_error_response('Sorry, Please try again.');
			}
		}catch(\Exception $e){
			return CommonApiHelper::return_error_response('Sorry, Please try again.');
		}
    } 
    
    // CMS pages content
    public function actionCmsPages() {

        //validate webservice
        $requiredParams = ['page_id'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];

        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);

        $page_id = $post['page_id'];
        
        //webview link for CMS page
        $url = Yii::getAlias('@backendURL') . '/site/cms?id='.$page_id;
        
        if (!empty($url)) {
            $response[]['link'] = $url;
            return CommonApiHelper::generate_success_response("",$response);
        }else{
            return CommonApiHelper::generate_error_response('1', 'Sorry, Please try again.');
        }
    } 
	
	/*//Search users
    public function actionSearchUsers() {

        //validate webservice
        $requiredParams = ['user_id','search_term'];

        CommonApiHelper::validateRequestParameters($requiredParams);

        $response = [];

        //Get request parameters.
        $post = Yii::$app->request->bodyParams;
        $post = array_map('trim', $post);
        
        //Fetch search term
        $user_id = $post['user_id'];
        $search_term = trim($post['search_term']);
        try{
			if(!empty($search_term)){
				//Subquery to check following status of logged in user.
				$current_user_following = Users::find()
						->select(['user_id as following_status','followers.user2_id as follower_user2_id'])
						->leftJoin('followers','followers.user1_id=users.user_id')
						->where(['user_id'=>$user_id]);

				//Fetch list of users most relevant to search query
				$searchedUsers = Users::find()
						->select(['users.*','CUF.*'])
						->leftJoin(['CUF' => $current_user_following],'CUF.follower_user2_id=users.user_id')
						->where(["or","name like '".$search_term."%'","username like '".$search_term."%'","email like '".$search_term."%'","phone_number like '".$search_term."%'"])
						->andWhere(["not",['user_id'=>$user_id]])
						//->orWhere("username REGEXP '[[:<:]]".$search_term."[[:>:]]'")
						//->orWhere("email REGEXP '[[:<:]]".$search_term."[[:>:]]'")
						//->orWhere("phone_number REGEXP '[[:<:]]".$search_term."[[:>:]]'");
						->asArray()
						->all();
						
				//var_dump($searchedUsers->createCommand()->getRawSql());exit;        
				if (!empty($searchedUsers)) {
					foreach ($searchedUsers as $resUser){
						//generate user response
						$response[]                = [
							'user_id'		=>	$resUser['user_id'],
							'username'		=>	$resUser['username'],
							'phone_number'	=>	$resUser['phone_number'],
							'name'			=>	$resUser['name'],
							'email'			=>	$resUser['email'],
							'phone_number'	=>	$resUser['phone_number'],
							'gender'		=>	$resUser['gender'],
							'about_me'		=>	$resUser['about_me'],
							'is_following'		=>	empty($resUser['following_status'])?'0':'1',
							'latitude'		=>	$resUser['latitude'],
							'longitude'		=>	$resUser['longitude'],
							'city'			=>	$resUser['city_id'],
							'state'			=>	$resUser['state_id'],
							'country'		=>	$resUser['country_id'],
							'birth_date'	=>	!empty($resUser['birth_date']) ? date('m/d/Y', strtotime($resUser['birth_date'])) : '',
							'profile_pic'	=>	!empty($resUser['profile_pic']) ? Yii::getAlias('@host').'/uploads/profile_pic/'.$resUser['profile_pic'] : ""
						];                
					}
					return CommonApiHelper::generate_success_response("",$response);
				}else{
					return CommonApiHelper::generate_error_response('1', 'No record found.');
				}
			}else{
				return CommonApiHelper::generate_error_response('1', 'Please enter search keyword.');
			} 
		}catch(\Exception $e){
			return CommonApiHelper::generate_error_response('1', 'Sorry, Please try again.');
		}		
        
    }*/
}