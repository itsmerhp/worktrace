<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use budyaga\cropper\Widget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\RestaurantOtp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => true,'enableAjaxValidation' => true]); ?>
    <div class="box-body">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?php echo $form->field($model, 'image',['options'=>['class'=>'form-group field-restaurant-restaurant_logo  col-xs-12 col-sm-12 col-md-12 col-lg-6','style'=>'padding:0']])->widget(Widget::className(), [
						'uploadUrl' => yii\helpers\Url::toRoute('/cuisines/uploadPhoto'),
						'maxSize'	=>	5242880,
						'cropAreaWidth'	=>	500,
						'cropAreaHeight'	=>	500,
						'height'	=> 500,
						'width'	=> 500,
					]); ?>
				<?php //echo $form->field($model, 'image')->textarea(['rows' => 6]) ?>
				<?php //echo $form->field($model, 'status')->textInput() ?>
			</div>
		</div>
	</div>
    <div class="box-footer">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['cuisines']), ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
