<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body"> 

    <?= $form->field($model, 'page_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'page_content')->textarea(['rows' => 6,'class' => 'form-control ckeditor']) ?>

    <?= $form->field($model, 'status')
            ->dropDownList(
                Yii::$app->params['STATUS_SELECT'],
                ['prompt'=>'Select']
            ); 
        ?>

    </div>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
