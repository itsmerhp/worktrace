<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', $model->page_name);
$this->params['breadcrumbs'][] = $model->page_name;
?>
<div class="login-box">
    <?php if(!empty($model)){ ?>
    <div class="login-logo">
		<a href="javascript:void(0);"><b><?= Html::encode($model->page_name) ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" id="login_box">
        <?= $model->page_content; ?>
    </div>
    <?php }else{ ?>
        <div class="login-logo">
            <a href="javascript:void(0);"><b> <?=Yii::t('app', 'No Page Found');?> </b></a>
        </div>
    <?php } ?>
</div>