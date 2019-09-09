<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app', 'Password Reset Successfully');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <div class="col-lg-12 well bs-component">

        <p><?= Yii::t('app', 'Password has been set successfully.') ?></p>

    </div>

</div>