<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Users;
use common\models\Restaurants;
use common\models\MenuItems;
use common\models\Posts;

/* @var $this yii\web\View */
$this->title = 'Change Password';
?>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
        Change Password
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?= Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active">Change Password</li>
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
            <?php //$form = ActiveForm::begin( ['id' => 'routingForm', 'enableClientValidation' => true, 'enableAjaxValidation' => true]); ?>
            <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
              <div class="box-body"> 
                <?= $form->field($model, 'currentPassword')->passwordInput(['maxlength' => 255, 'class'=> 'form-control', 'placeholder'=>'Current Password']) ?>
                <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => 255, 'class'=> 'form-control', 'placeholder'=>'New Password']) ?>
                <?= $form->field($model, 'retypePassword')->passwordInput(['maxlength' => 255, 'class'=> 'form-control', 'placeholder'=>'Re-type New Password']) ?>              
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['site/index']), ['class' => 'btn btn-danger']) ?>
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
              </div>
            <?php ActiveForm::end(); ?>
          </div>
          <!-- /.box -->         
        </div>
        <!--/.col (left) -->       
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
