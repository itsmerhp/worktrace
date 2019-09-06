<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RestaurantOtp */

$this->title = 'Update Restaurant Otp';
$this->params['breadcrumbs'][] = ['label' => 'Restaurant Otps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<section class="content-header">
	<h1>
	    <?= Html::encode($this->title) ?>
	</h1>
	<ol class="breadcrumb">
	    <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	    <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>/restaurant-otp">Restaurant OTPs</a></li>
	    <li class="active"><a href="javascript:void(0)"><?= $model->email; ?></a></li>
	</ol>
</section>
<!-- Main content -->
<section class="content">
  	<div class="row">
        <!-- left column -->
        <div class="col-md-12">
        	<!-- general form elements -->
    		<div class="box box-primary">
		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
			</div>
		</div>
	</div>
</section>
</div>