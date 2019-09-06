<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailFormat */
/* @var $form yii\widgets\ActiveForm */
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      <?= $this->title; ?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?= Yii::getAlias('@backendURL'); ?>/email-format"><i class="fa fa-dashboard"></i> Email Format</a></li>
    <li class="active"><?= $this->title;?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
                <!-- /.box-header -->
                <!-- form start -->

            <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
            <div class="box-body"> 
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'body')->textarea(['rows' => 6,'class'=>'form-control ckeditor']) ?>

                <?= $form->field($model, 'status')
                    ->dropDownList(
                        Yii::$app->params['STATUS_SELECT'],
                        ['prompt'=>'Select']
                    ); 
                ?>

                <?php 
                /*
                 *  <?= $form->field($model, 'created_at')->textInput() ?>
                    <?= $form->field($model, 'updated_at')->textInput() ?>
                 * 
                 */
                ?>
            </div>
            <div class="box-footer">
                <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['email-format/index']), ['class' => 'btn btn-danger']) ?>
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
      <!-- /.box -->         
    </div>
    <!--/.col (left) -->       
  </div>
  <!-- /.row -->
</section>
