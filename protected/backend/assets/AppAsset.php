<?php
/**
 * -----------------------------------------------------------------------------
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * -----------------------------------------------------------------------------
 */

namespace backend\assets;

use yii\web\AssetBundle;
use Yii;

// set @themes alias so we do not have to update baseUrl every time we change themes
Yii::setAlias('@themes', Yii::$app->view->theme->baseUrl);

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 *
 * @since 2.0
 *
 * Customized by Nenad Živković
 */
class AppAsset extends AssetBundle
{
    /*
    public $basePath = '@webroot';
    public $baseUrl = '@themes';
    
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
    ];
    */
    
    public $sourcePath = '@backend/';
    /*public $publishOptions = [
        'forceCopy' => true,
    ];*/
    public $css = [                    
                    'include/bootstrap/css/bootstrap.min.css',
                    'include/dist/css/AdminLTE.min.css',
                    'include/dist/css/skins/_all-skins.min.css',
                    'include/dist/css/style.css',
					'include/dist/css/image-picker.css',
                    'include/plugins/iCheck/square/blue.css',
                    'include/plugins/morris/morris.css',
                    'include/plugins/jvectormap/jquery-jvectormap-1.2.2.css',
                    'include/plugins/datepicker/datepicker3.css',
                    'include/plugins/daterangepicker/daterangepicker.css',
                    'include/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'
                ];
    public $js = [
                    'include/plugins/jQueryUI/jquery-ui.min.js',
                    'include/plugins/raphael/raphael-min.js',
                    'include/plugins/morris/morris.min.js',
                    'include/plugins/sparkline/jquery.sparkline.min.js',
                    'include/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
                    'include/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
                    'include/plugins/knob/jquery.knob.js',
                    'include/plugins/moment/moment.min.js',
                    'include/plugins/daterangepicker/daterangepicker.js',
                    'include/plugins/datepicker/bootstrap-datepicker.js',
                    'include/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
                    'include/plugins/slimScroll/jquery.slimscroll.min.js',
                    'include/plugins/fastclick/fastclick.js',        
                    'include/dist/js/app.js',
                    //'admin-lte/dist/js/pages/dashboard.js',
                    //'admin-lte/dist/js/demo.js',
                    'include/plugins/iCheck/icheck.min.js',
                    'https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js',
                    'include/common.js',
					'include/dist/js/image-picker.js'
                ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
