<?php 

namespace common\components;

use yii;
use yii\base\Component;
use yii\db\Query;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

// use Imagine\Gd;
// use Imagine\Image\Box;
// use Imagine\Image\BoxInterface;


class Generallib extends Component
{
	
	/**
	 * initilize the function when you calling any of the class function
	 * @return {[type]} [description]
	 */
	public function init()
	{

	}

	/**
	 * Simple test funtion for checking functinality works.
	 * @return [type] [description]
	 */
	public function testfunction()
	{
		return 'Hello world!';
	}

    public function trimarr($arr)
    {
        return (is_array($arr)) ? array_map(array($this, 'trimarr'),$arr) : trim($arr);
    }

	/**
	* This function create the directory if it is not exists.
	* @return boolean 
	*/
    public function createDir($path)
    {
        if (!file_exists($path)) 
        {
            mkdir($path, 0777, true);
        }
    }

	/**
	* This function create the random password for lenght of 4 char.
	* @return string $pass 
	*/
    public function createRandomPassword($length = 4) {

        $chars = "53b707b53499965f93905d7c8f59e1f1146c05aeabcdefghijkmnopqrstuvwxyz023456789";
        //srand((double) microtime() * 1000000);

        $i = 0;
        $pass = '';

        while ($i <= $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }

    /**
    * This function create the random password for lenght of 4 char.
    * @return string $pass 
    */
    public function createRandomUniqueNumber($length = 4) {

        $chars = "0123DOTR45RANDOM6789ABCDE01245FGHIJKL85462MNOPQRSTU123485VWXYZ";
        $i = 0;
        $pass = '';

        while ($i <= $length) {
            $num = rand() % 64;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * This function generate a unique random number for the specified length.
     * @param  integer $length size of the generated unique number
     * @param  string  $table  database table name for checking the uniqueness of the number
     * @param  string  $field  database table field name for checking the uniqueness of that column.
     * @return string          generated unique number.
     */
    public function createRandomNumber($length = 4,$table = NULL,$field = NULL)
    {
        $uniquenumber = Yii::$app->generallib->createRandomUniqueNumber($length);
        
        if(!empty($table) && !empty($field))
        {
            $query = (new Query())
            ->select($field)
            ->from($table)
            ->where("autoUniqueNumber = :autoUniqueNumber",[':autoUniqueNumber'=>$uniquenumber])
            ->scalar();

            if(!empty($query))
            {
                return $this->createRandomNumber($length,$table);
            }
        }
        return $uniquenumber;
    }

    /**
     * Sent push notification code for Iphone
     * @param  string $deviceToken [description]
     * @param  array  $payloadArr  [description]
     * @return boolean              
     */
    public function notificationIphone($deviceToken=NULL,$payloadArr = [])
    {
        //include("./includes/JSON.php");
        
        $payload = [];
        $payload['aps'] =$payloadArr;
        $payload = json_encode($payload);
        $apnsHost = 'gateway.sandbox.push.apple.com';
        //$apnsHost = 'gateway.push.apple.com';
        $apnsPort = 2195;
        $apnsCert = Yii::getAlias('@rootpath').'/keys/dev.pem';
        
        // Put your private key's passphrase here:
        $passphrase = '';
        
        $streamContext = @stream_context_create();
        @stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
        //@stream_context_set_option($streamContext, 'ssl', 'passphrase', $passphrase);

       $apns = @stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
        //$apns = @stream_socket_client('tls://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
        $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
        $result = @fwrite($apns, $apnsMessage); 
        if (!$result)
        {
           
        }
        else
        {
           
        }       
        if(!$apns)
        {
            // Close the connection
            fclose($apns);
            return 0;
        }
        else
        {
           // Close the connection
           fclose($apns);
           return 1;
        }
    }
    
   	/**
   	 * This function send the notification to android device
   	 * @param  string $registatoin_ids 
   	 * @param  array $message         
   	 * @return boolean                 
   	 */
    public function notificationAndroid($registatoin_ids, $message) 
    { 
        // include config
        //include_once './config.php';
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
 
        $fields = [
            'registration_ids' => [$registatoin_ids],
            'data' => $message,
        ];

        //define("GOOGLE_API_KEY", "AIzaSyDTZoJCLRu7HpWV0f6kjbw0YMNcK46HmD0");
        $headers = [
            'Authorization: key=AIzaSyA4gOjczGyqHLW8F8KmUukqnEUhLH86bs8',
            'Content-Type: application/json'
        ];

        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
           
        }
        else
        {
           
        }
 
        // Close connection
        curl_close($ch);

        return $result;
    }

    /**
    * This function convert the seconds in to human readble form.
    * @param  int $date1 timestamp of firstdate
    * @return string        
    */
    public function timediff($date1) 
    {

		$date2   = time();
		$diff    = abs($date2 - $date1);
		$years   = floor($diff / (365 * 60 * 60 * 24));
		$months  = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
		$days    = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
		$hours   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
		$minuts  = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
		$seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));

        if ($diff < 60)
            return 'Just now';
        if ($diff < 3600)
            return $minuts . " min ago";
        if ($diff < 86400)
            return ($hours > 1) ? $hours ." hrs ago" : $hours ." hr ago";
        if ($diff < 604800)
            return ($days > 1) ? $days . " days ago" : $days . " day ago";
        if($diff< 2592000) 
            return FLOOR($diff / 604800)." week ago";
        if ($diff < 31536000)
            return ($months > 1) ? $months . " months ago" : $months . " month ago";
        else
            return ($years > 1) ? $years . " yrs ago" : $years . " yr ago" ;
    }

	/**
	* This function generate the token for compare with the app token.
	* @param  string $nonce     
	* @param  int $timestamp unix timestamp
	* @return string newly generated token          
	*/
    Public function generateToken($nonce,$timestamp) {      
       
        $hash_str       = "";
        $secret         = Yii::$app->params['secret'];
        $private_key    = Yii::$app->params['private_key'];
        $hash_hmac_algo = Yii::$app->params['hash_hmac_algo'];
        $hash_str       = "nonce=".$nonce."&timestamp=".$timestamp."|".$secret;
        //echo "\nsecret : ".$secret."\nprivate_key : ".$private_key."\nhash_hmac_algo : ".$hash_hmac_algo."\n hash_str : ". $hash_str; 
        $sig = hash_hmac($hash_hmac_algo, $hash_str , $private_key);
        return $sig;
    }

	/**
	* This function validate the token generated by APP.
	* @param  array $data 
	* @return array       
	*/
    public function validateToken($data) {
        $response = [];
        
        if(empty($data['token']))
        {
            $response['code'] = "10";
            $response['message']         = 'Token can not be blank';
            return $response;
        }
        if(empty($data['nonce']))
        {
            $response['code'] = "11";
            $response['message']         = 'Nonce value can not be blank';
            return $response;
        }
        if(empty($data['timestamp']))
        {
            $response['code'] = "12";
            $response['message']         = 'Timestamp value can nt be blank.';
            return $response;
        }

        $sig = $this->generateToken($data['nonce'],$data['timestamp']);
       //echo "\nToken : ".$data['token']."\nNonce : ".$data['nonce']."\ntimestamp : ".$data['timestamp']; die;
        if ($sig !== $data['token']) {
            $response["code"] = "13";
            $response["message"]         = 'Sorry! Invalid token value.';         
        }
        return $response;
    }

    /**
     * This function is used for making the resize image from the original image.
     * @param  string $resizeImagePath reletive path of the original image 
     * @param  string $folderPath      reletive path of the folder where to save the resize image.
     * @param  string $resizeImageName name of the resize image 
     * @param  string $width width of the thumbnail. Default is 200px.
     * @param  string $height height of the thumbnail. Default is 200px.
     * @param  string $quality preserve quality of the original image. Default is 90%
     * @return string image path and name
     */
    public function makeResizeImage($resizeImagePath,$folderPath,$resizeImageName,$width=200,$height=200,$quality=90)
    {
        $imagine = Image::getImagine()
        ->open($resizeImagePath)
        ->resize(new Box($width, $height))
        ->save($folderPath.$resizeImageName, ['quality' => $quality]);

        //$thumbimage = Image::thumbnail($resizeImagePath, $width, $height)->save($folderPath."t_".$resizeImageName,['quality' => $quality]);
        
        return $folderPath.$resizeImageName;
    }

    /**
     * Get the status value in human readable format.
     * @param  int|null $key 1 - Active, 2 - Inactive
     * @return array      
     */
    public function getStatus($key=NULL){
        $data = array(
            "1" =>"Active",
            "2" =>"Inactive"
        );
        if(!is_null($key)){
            return $data[$key];
        }
        return $data;
    }

     /**
     * Get the Boolean value in human readable format.
     * @param  int|null $key 1 - Yes, 2 - No
     * @return array      
     */
    public function getYesno($key=NULL){
        $data = array(
            "1" =>"Yes",
            "2" =>"No"
        );
        if(!is_null($key)){
            return $data[$key];
        }
        return $data;
    }
    /**
     * [getYeardropdown generate year dropdown]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getYeardropdown($key=NULL) {
        
        $years = range(date('Y'), date('Y') + 10);
        foreach ($years as $year)
        {
            $data[$year] = $year;
        }
        if(!is_null($key)){
            return $data[$key];
        }
        return $data;
    }
    /**
     * [getMonthdropdown generate month dropdown]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getMonthdropdown($key=NULL) {
    
        for($i=1;$i<=12;$i++)
        {
            //$data[$i] = date('F', mktime(0, 0, 0, $i, 10));
            $data[str_pad($i, 2, 0, STR_PAD_LEFT)] = str_pad($i, 2, 0, STR_PAD_LEFT);
        }
    
        if(!is_null($key)){
            return $data[$key];
        }
        return $data;
    }
    /**
     * [cctype to get credit card type]
     * @param  [type]  $cc          [description]
     * @param  boolean $extra_check [description]
     * @return [type]               [description]
     */
    public function cctype($cc, $extra_check = false){

        $cards = array(
            "visa"       => "(4\d{12}(?:\d{3})?)",
            "amex"       => "(3[47]\d{13})",
            "jcb"        => "(35[2-8][89]\d\d\d{10})",
            "maestro"    => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
            "solo"       => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
            "mastercard" => "(5[1-5]\d{14})",
            "switch"     => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
            "dinersclub" => "(3(?:0[0-5]|[68][0-9])[0-9]{11})",
            "discover"   => "(6(?:011|5[0-9]{2})[0-9]{12})",
        );

        $names   = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch", "Diners Club", "Discover");
        $matches = array();
        $pattern = "#^(?:".implode("|", $cards).")$#";

        $result  = preg_match($pattern, str_replace(" ", "", $cc), $matches);
        if($extra_check && $result > 0){
            $result = (validatecard($cc))?1:0;
        }

        $cards = array_keys($cards);
        return ($result>0)?$cards[sizeof($matches)-2]:false;
    }
    /**
     * [is_valid_card to check card is valid or not]
     * @param  [type]  $number [description]
     * @return boolean         [description]
     */
    public function is_valid_card($number)
    {
        // Remove Spaces and hyphens for some credit cards
        $number        = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length = strlen($number);
        $parity        = $number_length % 2;

        // Apply Luhn's algorthm for the number
        $total  = 0;
        for ($i = 0; $i<$number_length; $i++)
        {
            $digit=$number[$i];
            if ($i % 2 == $parity)
            {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9)
                {
                    $digit-=9;
                }
            }
            // Add all the digits
            $total+=$digit;
        }
        // If the modulo 10 equals 0, then number is valid
        if($total % 10 == 0)
        {
            return cctype($number);
        }
        else
            return "Invalid Card";
    }
}
	
?>