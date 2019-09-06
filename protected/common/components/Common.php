<?php 

namespace common\components;

use common\models\Users;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

class Common
{
	public static function generate_image_name($image_name = ''){
		return md5($image_name).rand(99999999, 9999999);
	}
	public static function explode_using_multiple_delimeters( $delimiters, $string )
    {
        $string = explode( chr( 1 ), str_replace( $delimiters, chr( 1 ), $string ) );
        return array_values(array_filter(array_map('trim',$string)));
    }
}	
?>