<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RestaurantOtp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => true,'enableAjaxValidation' => true]); ?>
    <div class="box-body"> 
		<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

		<?php /*echo $form->field($model, 'status')
            ->dropDownList(
                Yii::$app->params['STATUS_SELECT'],
                ['prompt'=>'Select']
            ); */
        ?>
	</div>
    <div class="box-footer">
		<?= Html::submitButton($model->isNewRecord ? 'Send OTP' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['restaurant-otp']), ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
