<?php

namespace api\components;

use common\models\Users;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\Posts;
use common\models\UsersAccessTokens;
use yii\helpers\ArrayHelper;

class CommonApiHelper {

    // Convert all array values to string recursively
    public static function convert_array_values_string($data_to_trim) {
        foreach ($data_to_trim as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $data_to_trim[$key] = CommonApiHelper::convert_array_values_string($value);
            } else {
                $value = is_null($value) ? '' : $value;
                $data_to_trim[$key] = (string) $value;
            }
        }
        return $data_to_trim;
    }

    //Replace NULL value with blank string
    public static function replacenull($array) {
        if (!empty($array)) {
            array_walk($array, function (&$value, $key) {

                if (is_null($value)) {
                    $value = '';
                }
            });
        }
        return $array;
    }

    //Function for success reponse.
    public static function generate_success_response($message = '', $data = [], $extraResponse = []) {
        $amReponseParam = CommonApiHelper::replacenull($data);
        $amResponse = array('error' => "0", 'message' => $message, 'data' => CommonApiHelper::convert_array_values_string($data));

        //Code for extra params in success response.
        if (!empty($extraResponse)) {
            foreach ($extraResponse as $key => $value) {
                //$amResponse[$key] = (string)$value;
                $amResponse[$key] = $value;
            }
        }
        return $amResponse;
    }

    //New function for success reponse.
    public static function return_success_response($message = '', $data = [], $extraResponse = []) {
        $amReponseParam = CommonApiHelper::replacenull($data);
        $amResponse = array(
            'status' => "1",
            'message' => $message,
            'data' => CommonApiHelper::convert_array_values_string($data)
        );

        //Code for extra params in success response.
        if (!empty($extraResponse)) {
            foreach ($extraResponse as $key => $value) {
                $amResponse[$key] = (string) $value;
                //$amResponse[$key] = $value;
            }
        }
        return $amResponse;
    }

    //New function for error response.
    public static function return_error_response($message = '', $error_flg = "0") {
        $amResponse = array(
            'status' => (string) $error_flg,
            'message' => $message,
            'data' => []
        );
        return $amResponse;
    }

    //Function for error response.
    public static function generate_error_response($error_flg = "1", $message = '') {
        $amResponse = array('error' => (string) $error_flg, 'message' => $message, 'data' => []);
        return $amResponse;
    }

    //Function to validate request parameters for web services.
    public static function validateRequestParameters($required) {
        $request = Yii::$app->request->bodyParams;

        //Check if any parameter is sent or not.
        $errorMessage = "";
        if (!empty($request)) {
            //Fetch keys of request parameters
            //$request = array_filter($request);
            $request = array_keys($request);

            //Find difference between required and request parameters
            $validateArr = array_diff($required, $request);

            //Check if any request parameter is missing or not.
            if (count($validateArr) > 0) {
                $errorMessage = implode(", ", array_values($validateArr));
            }
        } else {
            $errorMessage = implode(", ", $required);
        }
        if (!empty($errorMessage)) {
            return CommonApiHelper::encodeResponseJSON(CommonApiHelper::return_error_response($errorMessage, "-3"));
        } else {
            return;
        }
    }

    //Authenticate access token of user in web service
    public static function checkAuthentication($accessToken, $user_id) {
        $valid = 0;
        //Check user exist with given access token or not.
        $chkAuthentication = Users::findAll(["access_token" => $accessToken, "user_id" => $user_id]);
        if (!empty($chkAuthentication)) {
            foreach ($chkAuthentication as $value) {
                if ($value->access_token == $accessToken) {
                    $valid = 1;
                }
            }
        } else {
            $valid = 0;
        }

        if ($valid != 1) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID
            $errormessage['error'] = "1";
            $errormessage['message'] = 'Auth token not valid.';
            $errormessage['data'] = array();
            CommonApiHelper::encodeResponseJSON($errormessage);
        }

        return;
    }

    //Function to check user is active/inactive
    public static function checkUserStatus($user_id, $access_token) {
        //Fetch user details
        $chkUserStatus = Users::findOne(["user_id" => $user_id]);
        //Fetch user access tokens
        $usersAccessTokens = ArrayHelper::getColumn(UsersAccessTokens::find()->where(["user_id" => $user_id])->all(), 'access_token');
        $errorCode = $errorMessageTitle = $errorMessage = "";
        if (empty($chkUserStatus) || ($chkUserStatus->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT']))) {
            // FOR GENERATE ERROR RESPONSE IF USER IS INACTIVE
            $errorCode = "-1";
            $errorMessageTitle = 'You are Inactive:';
            $errorMessage = 'Please contact Administrator for more details.';
        } elseif (!in_array($access_token, $usersAccessTokens)) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID			
            $errorCode = "-2";
            $errorMessageTitle = 'Auth token not valid.';
        }
        if (!empty($errorCode)) {
            return CommonApiHelper::encodeResponseJSON(CommonApiHelper::return_error_response($errorMessageTitle, $errorMessage, $errorCode));
        } else {
            return;
        }
    }

    //Check post exist or not before any action has been performed on post
    public static function postExistanceCheck($post_id) {
        //Check post is exist or not.
        $chkPostStatus = Posts::findOne(["id" => $post_id]);

        if (empty($chkPostStatus)) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID
            $errormessage['error'] = "-3";
            $errormessage['message'] = 'This post is not exist.';
            $errormessage['data'] = array();
            CommonApiHelper::encodeResponseJSON($errormessage);
        }

        return;
    }

    /**
     * function: encodeResponseJSON()
     * For generate random number
     *
     * @param array   $amResponse
     * @return object JSON
     */
    public static function encodeResponseJSON($amResponse) {

        header('Content-type:application/json');
        echo Json::encode($amResponse);
        Yii::$app->end();
    }

    //Print array 
    public static function p($arr, $ex = "false") {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        if ($ex == true)
            exit;
    }

    //convert big number in K,M,B,T format(e.g. distance)
    public static function short_big_number($value) {

        //Convert miles in to feet
        $toFeet = ($value * 5280);

        //Return distance converted in feet if distance is below 500 feet.
        if ($toFeet <= 500) {
            return round($toFeet, 1) . "ft";
        }

        if ($value >= 10000)
            return '10k mi+';
        if ($value > 1000)
            return round(($value / 1000), 1) . 'k mi';

        return round(($value), 1) . "mi";
    }

    //Zip code to address using google api
    public static function zipcode_to_address($zipcode) {
        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$zipcode&sensor=false");
        $json = json_decode($json);

        if (!empty(array_filter($json->{'results'}))) {
            return $address = $json->{'results'}[0]->{'formatted_address'};
        } else {
            return false;
        }
    }

    /**
     * function: MailTemplate()
     * For send mail.
     *
     * @param Array   $replaceString
     * @param string  $Url
     */
    public static function MailTemplate($replaceString, $body) {

        $logo_url = Yii::getAlias('@backendURL');
        //$logo_img_url   = Yii::$app->params['site_url'] . Yii::$app->request->baseUrl . "/img/logo-big.png";
        $logo_img_url = Yii::getAlias('@host') . '/' . Yii::$app->params['logo_url'];

        if (!empty($replaceString)) {
            foreach ($replaceString as $key => $value) {
                $replacekey[] = $key;
                $replacevalue[] = $value;
            }
        }

        $replacekey[] = '{logo_front_url}';
        $replacekey[] = '{logo_img_url}';
        $replacekey[] = '{logo_url}';
        $replacevalue[] = "javascript:void(0);";
        $replacevalue[] = $logo_img_url;
        $replacevalue[] = $logo_url;
        $result = str_replace(
                $replacekey, $replacevalue, $body
        );
        return $result;
    }

    public static function smart_resize_image($file, $string = null, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false, $quality = 100, $grayscale = false, $path = ''
    ) {

        if ($height <= 0 && $width <= 0)
            return false;
        if ($file === null && $string === null)
            return false;
        # Setting defaults and meta
        $info = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        //p($info);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        # Calculating proportionality
        if ($proportional) {
            if ($width == 0)
                $factor = $height / $height_old;
            elseif ($height == 0)
                $factor = $width / $width_old;
            else
                $factor = min($width / $width_old, $height / $height_old);
            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        # Loading image to memory according to type
        switch ($info[2]) {
            case IMAGETYPE_JPEG: $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_GIF: $file !== null ? $image = imagecreatefromgif($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_PNG: $file !== null ? $image = imagecreatefrompng($file) : $image = imagecreatefromstring($string);
                break;
            default: return false;
        }

        # Making the image grayscale, if needed
        if ($grayscale) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }

        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $width, $height, $width_old, $height_old);


        # Taking care of original, if needed
        if ($delete_original) {
            if ($use_linux_commands)
                exec('rm ' . $file);
            else
                @unlink($file);
        }
        # Preparing a method of providing result
        /* switch ( strtolower($output) ) {
          case 'browser':
          $mime = image_type_to_mime_type($info[2]);
          header("Content-type: $mime");
          $output = NULL;
          break;
          case 'file':
          $output = $file;
          break;
          case 'return':
          return $image_resized;
          break;
          default:
          break;
          } */
        $ext = pathinfo($output, PATHINFO_EXTENSION);

        $output = basename($output, $ext);

        # Writing image according to type to the output destination and image quality
        switch ($info[2]) {
            case IMAGETYPE_GIF: $output .= "gif";
                imagegif($image_resized, $path . $output);
                break;
            case IMAGETYPE_JPEG: $output .= "jpeg";
                imagejpeg($image_resized, $path . $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $output .= "png";
                $quality = 9 - (int) ((0.9 * $quality) / 10.0);
                imagepng($image_resized, $path . $output, $quality);
                break;
            default: return false;
        }
        //p($image_resized);
        return $output;
    }

    /* //Send push notification in android devices
      function sendFCMPushnotification($arr) {
      $device_token = $arr['device_token'];
      $message = $arr['message'];
      $id = $arr['id'];
      $badge = $arr['Badge'];
      $type = $arr['type'];

      $url = 'https://fcm.googleapis.com/fcm/send';

      $fields = array (
      'registration_ids' => array (
      $device_token
      ),
      'data' => array (
      "message" => $message,
      "id"    =>  $id,
      "badge"    =>  $badge,
      "type"    =>  $type,
      "sound"    =>  "default"
      ),
      'notification' => array(
      'body'  => $message,
      'title' =>  "Crityk",
      'click_action'  =>  'NotificationListingActivity'

      )
      );
      $fields = json_encode ( $fields );

      $headers = array (
      'Authorization: key=' . Yii::$app->params['FCM_TOKEN'],
      'Content-Type: application/json'
      );

      $ch = curl_init ();
      curl_setopt ( $ch, CURLOPT_URL, $url );
      curl_setopt ( $ch, CURLOPT_POST, true );
      curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
      curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
      curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

      $result = curl_exec ( $ch );
      curl_close ( $ch );
      } */

    //Send push notification in android devices
    public static function sendFCMPushnotification($options, $type = 1) {
        $device_tokens = $options['device_token'];
        $data['sound'] = 'default';
        $data['dish_id'] = $options['dish_id'];

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array('registration_ids' => $device_tokens);
        //Android
        if ($type == 1) {
            $data['message'] = $options['message'];
            $fields['data'] = $data;
            //ios
        } else {
            $data['body'] = $options['message'];
            $fields['notification'] = $data;
        }
        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . Yii::$app->params['FCM_TOKEN'],
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    // Send Push Notification to Members - using iphone
    public static function SendNotification($arr) {
        $deviceid = $arr['device_token'];
        //$pemfielname        = 'apns-dev-minime.pem';
        $pemfielname = Yii::$app->params['push_notification_pem_file']['distribution'];
        //$unreadCount =   StoryCommentUserTagged::find()->where(['user_id'=>$arr['user_id'],'is_viewed' => 0])->count();

        $amAPNSRequest = array(
            'apns_host' => Yii::$app->params['ios_push_notification_gateway_url']['distribution'],
            'apsn_certificate' => $pemfielname,
            'apns_pass_pharse' => '',
            'id' => $arr['id'],
            'ssMessage' => $arr['message'],
            'Badge' => $arr['Badge'],
            'type' => $arr['type'],
            /* 'notification_type' => $arr['notification_type'], */
            'sound' => 'default',
        );

        CommonApiHelper::sendAPNS($deviceid, $amAPNSRequest);
    }

    public static function sendAPNS($ssDeviceToken, $amAPNSReques, $ssTags = 201) {
        $ssApnsHost = $amAPNSReques['apns_host'];
        $ssApnsCert = $amAPNSReques['apsn_certificate'];
        $ssPassPhrase = $amAPNSReques['apns_pass_pharse'];
        $ssBadgeCount = $amAPNSReques['Badge'];
        $passphrase = '';
        $ssCertifiateFilePath = $ssApnsCert;
        //p($ssCertifiateFilePath,0);
        //p(file_exists($ssCertifiateFilePath));
        if (file_exists($ssCertifiateFilePath)) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $ssCertifiateFilePath);
            // Open a connection to the APNS server
            $oFp = stream_socket_client($ssApnsHost, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            //p($oFp);
            if ($oFp) {
                try {
                    // Create the payload body
                    $amBody['aps'] = array(
                        'id' => $amAPNSReques['id'],
                        'alert' => $amAPNSReques['ssMessage'],
                        'tag' => $ssTags,
                        'badge' => (int) $amAPNSReques['Badge'],
                        'type' => $amAPNSReques['type'],
                        /* 'notification_type' => $amAPNSReques['notification_type'], */
                        'sound' => $amAPNSReques['sound']);
                    // Encode the payload as JSON
                    $amEncodePayload = json_encode($amBody);
                    // Build the binary notification
                    $smEncodeMsg = chr(0) . pack('n', 32) . pack('H*', $ssDeviceToken) . pack('n', strlen($amEncodePayload)) . $amEncodePayload;
                    // Send it to the server
                    $oResult = fwrite($oFp, $smEncodeMsg, strlen($smEncodeMsg));
                    //p($oResult,0);
                    fclose($oFp);
                } catch (Exception $e) {
                    echo 'Caught exception: ' . $e->getMessage() . "\n";
                }
            }
        }
    }

    //display start rating
    public function displayStartRating($rating, $totalRating = 4) {
        $ratingHtml = "";
        for ($i = 0; $i < $rating; $i++) {
            $ratingHtml .= '<span class="fa fa-star checked"></span>';
        }
        for ($j = $rating; $j < $totalRating; $j++) {
            $ratingHtml .= '<span class="fa fa-star"></span>';
        }
        return $ratingHtml;
    }

    //generate access token
    public static function generateAccessToken($user_id, $refresh_token) {
        $token = Yii::$app->jwt->getBuilder()
                ->setIssuer(Yii::getAlias('@host'))
                ->setAudience(Yii::getAlias('@host'))
                //->setId(time(), true)
                ->setIssuedAt(time())
                //->setNotBefore(time() + 60) // Configures the time before which the token cannot be accepted (nbf claim)
                ->setExpiration(time() + 24 * 60 * 60)
                ->set('user_id', $user_id)
                ->set('refresh_token', $refresh_token)
                ->getToken();
        return (string) $token;
    }

    //validate access token
    public static function validateAccessToken() {
        try {
            $access_token = Yii::$app->request->headers->get('Authorization');
            if ($access_token) {
                $token = Yii::$app->jwt->getParser()->parse((string) $access_token); // Parses from a string
                $data = Yii::$app->jwt->getValidationData();
                if (!$token->validate($data)) {
                    self::encodeResponseJSON(self::return_error_response("Please pass valid access token.", "-2"));
                } else {
                    $refreshToken = $token->getClaim('refresh_token');
                    $userId = $token->getClaim('user_id');
                    $userDetails = UsersAccessTokens::find()
                            ->with(['user' => function ($query) {
                                $query->with(['company']);
                                return $query;
                            }])->where(['refresh_token'=>$refreshToken,'users_access_tokens.user_id'=>$userId])
                            ->one();
                    if($userDetails){
                        if($userDetails->user->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])){
                            self::encodeResponseJSON(self::return_error_response("Your account is Inactive. Please contact Administrator for more details.", "-1"));
                        }else if($userDetails->user->company->status == array_search('Inactive', Yii::$app->params['STATUS_SELECT'])){
                            self::encodeResponseJSON(self::return_error_response("Company is Inactive. Please contact Administrator for more details.", "-1"));
                        }else{
                            return $userDetails;
                        }
                    }else{
                        self::encodeResponseJSON(self::return_error_response("Please pass valid access token.", "-2"));
                    }
                }
            } else {
                self::encodeResponseJSON(self::return_error_response("Please pass access token.", "-2"));
            }
        } catch (\Exception $e) {
            self::encodeResponseJSON(self::return_error_response("Please pass valid access token.", "-2"));
        }
    }

}

?>