<?php
use backend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?=  Yii::$app->params['siteName']; ?> | <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
        $this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css");
        $this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css");
    ?>
</head>
<body class="hold-transition login-page">
    <?php $this->beginBody() ?>
        <div class="flash_message">
            <?php include_once 'flash_message.php'; ?>
        </div>

        <?= $content ?>   
    <?php $this->endBody() ?>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
</body>
</html>
<?php $this->endPage() ?>
