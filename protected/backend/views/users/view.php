<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = "User : ".$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
    <h1>
        <?= Html::encode($this->title) ?>
	</h1>
    
    <ol class="breadcrumb">
        <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li><a href="<?php echo Yii::getAlias('@backendURL'); ?>/users">Users</a></li>
        <li><a href="javascript:void(0)"><?= $this->title; ?></a></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body table-responsive">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute' => 'user_type',
						'value'  => Yii::$app->params['USER_TYPE_VALUES'][$model->user_type]
					],
					'name',
					'email:email',
					[
						'attribute'=>'profile_pic',
						'value'  => !empty($model->profile_pic) ? "<img src='".$model->profile_pic."' width='70px'/>" : "<img src='".Yii::getAlias('@host')."/uploads/no-image.png' width='70px'/>",
						'format' => 'html',
					],
					[
					   	'label'=>'Restaurant Name',
						'value'  => isset($model->restaurantDetails) ? $model->restaurantDetails->name : '',
					   	'visible' => $model->user_type == Yii::$app->params['USER_TYPE']['restaurant'] ? true : false,
					   	// here rest of your code
					],
					[
					   	'label'=>'Restaurant Mobile Number',
						'value'  => isset($model->restaurantDetails) ? $model->restaurantDetails->mobile : '',
					   	'visible' => $model->user_type == Yii::$app->params['USER_TYPE']['restaurant'] ? true : false,
					   	// here rest of your code
					],
					[
					   	'label'=>'Restaurant Address',
						'value'  => isset($model->restaurantDetails) ? $model->restaurantDetails->address : '',
					   	'visible' => $model->user_type == Yii::$app->params['USER_TYPE']['restaurant'] ? true : false,
					   	// here rest of your code
					],
					[
					   	'label'=>'Restaurant Name',
						'value'  => isset($model->restaurantDetails) ? $model->restaurantDetails->name : '',
					   	'visible' => $model->user_type == Yii::$app->params['USER_TYPE']['restaurant'] ? true : false,
					   	// here rest of your code
					],
					[
					   	'label'=>'Restaurant Currency',
						'value'  => isset($model->restaurantDetails->country) ? $model->restaurantDetails->country->currency_code.'('.$model->restaurantDetails->country->currency_symbol.')' : '',
					   	'visible' => $model->user_type == Yii::$app->params['USER_TYPE']['restaurant'] ? true : false,
					   	// here rest of your code
					]
				],
			]) ?>

		</div>
	</div>
</section>