<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app', 'Login');
?>
<div class="login-box">
    <div class="login-logo">
      <a href="javascript:void(0);"><b><?=Yii::t('app', 'Admin');?></b> <?=Yii::t('app', \Yii::$app->params['siteName']);?></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" id="login_box">
      <p class="login-box-msg"><?=Yii::t('app', 'Sign in to start your session');?></p>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <?php 
            $cookies_email = isset($_COOKIE[\Yii::$app->params['siteName'].'_admin_email']) ? $_COOKIE[\Yii::$app->params['siteName'].'_admin_email'] : ''; 
            $cookies_password = isset($_COOKIE[\Yii::$app->params['siteName'].'_admin_password']) ? $_COOKIE[\Yii::$app->params['siteName'].'_admin_password'] : ''; 
        ?>
        <?php //-- use email or username field depending on model scenario --// ?>
            <div class="form-group has-feedback">
                <?php //if ($model->scenario == 'lwe'): ?>
                    <?= $form->field($model, 'email')->textInput(['placeholder' => "Enter Your Email",'value'=>$cookies_email])->label(false); ?>        
                <?php /*else: ?>
                    <?= $form->field($model, 'username')->textInput(['placeholder' => "Enter Your Username"])->label(false); ?>
                <?php endif*/ ?>
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password','value'=>$cookies_password])->label(false) ?>
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
              <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe',['options' => ['class' => 'form-group checkbox icheck']])->checkbox() ?>
              </div>
              <!-- /.col -->
              <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
              </div>
              <!-- /.col -->
            </div>
            
            <!--<hr>
            Forgot your password ? <br/>

            no worries, click <a id="forget-password" href="javascript:void(0);" onclick="javascript:$('#login_box').hide();$('#forgot_box').show()"> here </a> to reset your password.-->

        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
    
    <!-- forgot password box body -->
    <div class="login-box-body" id="forgot_box" style = 'display: none'>
        <?php $form = ActiveForm::begin(['id' => 'forgot-password-form', 'enableAjaxValidation'=>true , 'options'=> ['class' => 'form-signin']]); ?>
                <h3 class="form-signin-heading">Forget Password ?</h3>
                <p>
                         Enter your e-mail address below to reset your password.
                </p>
                    <?= $form->field($forgotPasswordModel, 'email', ['labelOptions'=> ['label'=> false]])->textInput(['placeholder'=> 'Email'])->label(false) ?>
                <div class="form-actions">
                        <?= Html::Button('<i class="m-icon-swapleft"></i> Back', ['class' => 'btn btn-danger', 'name' => 'Back-button','id'=>'back-btn','type'=>'button', 'onclick'=> 'javascript: $("#forgot_box").hide();$("#login_box").show();']) ?>
                        <?= Html::submitButton('Submit <i class="m-icon-swapright m-icon-white"></i>', ['class' => 'btn btn-primary pull-right', 'name' => 'forgot-button']) ?>
                </div>
        <?php ActiveForm::end(); ?>
    </div>
    <!-- forgot password box body --> 
</div>
<!-- /.login-box -->   
<!--<div class="site-login">

    <h1 class="login"><?= Html::encode($this->title) ?></h1>

    <div class="col-lg-5 well bs-component">

        <p><?= Yii::t('app', 'Please fill out the following fields to login:') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?php //-- use email or username field depending on model scenario --// ?>
        <?php if ($model->scenario === 'lwe'): ?>
            <?= $form->field($model, 'email') ?>        
        <?php else: ?>
            <?= $form->field($model, 'username') ?>
        <?php endif ?>

        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
  
</div>-->
