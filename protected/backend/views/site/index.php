<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Users;
use common\models\Meals;
use common\models\Cuisines;
use common\models\RestaurantOtp;
use common\models\Dishes;
use common\models\QuickFoods;

/* @var $this yii\web\View */
$this->title = 'Dashboard';
?>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?= Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
		  <a href="<?= Yii::getAlias('@backendURL'); ?>/users">
			<!-- small box -->
			<div class="small-box bg-yellow">
			  <div class="inner">
				  <h3><?=  Users::find()->where(['role_id'=>  Yii::$app->params['USER_ROLES']['app_user']/*,'user_type'=>  Yii::$app->params['USER_TYPE']['user']*/,'status'=>  1])->count(); ?></h3>

				<p>Users</p>
			  </div>
			  <div class="icon">
				<i class="ion ion-person-add"></i>
			  </div>
			</div>
		  </a>
      </div>
      <?php /*<div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
          <div class="inner">
              <h3><?=  Users::find()->where(['role_id'=>  Yii::$app->params['USER_ROLES']['app_user'],'user_type'=>  Yii::$app->params['USER_TYPE']['restaurant'],'status'=>  1])->count(); ?></h3>
            <p>Restaurants</p>
          </div>
          <div class="icon">
            <i class="ion ion-home"></i>
          </div>
        </div>
      </div>*/?>
      <!-- ./col 
      <div class="col-lg-3 col-xs-6">
	  	<a href="<?= Yii::getAlias('@backendURL'); ?>/restaurant-otp">
			<div class="small-box bg-green">
			  <div class="inner">
				  <h3><?= RestaurantOtp::find()->where(['status'=>  1])->count(); ?></h3>

				<p>Restaurant OTPs</p>
			  </div>
			  <div class="icon">
				<i class="ion ion-archive"></i>
			  </div>
			</div>
		  </a>
      </div> -->
      <!-- ./col -->
		<div class="col-lg-3 col-xs-6">
			<a href="<?= Yii::getAlias('@backendURL'); ?>/cuisines">
				<!-- small box -->
				<div class="small-box bg-aqua">
				  <div class="inner">
					  <h3><?=  Cuisines::find()->where(['status'=>  1])->count(); ?></h3>
					<p>Cuisines</p>
				  </div>
				  <div class="icon">
					<i class="ion ion-android-list"></i>
				  </div>
				</div>
			</a>
      </div>
	<div class="col-lg-3 col-xs-6">
		<a href="<?= Yii::getAlias('@backendURL'); ?>/meals">
			<!-- small box -->
			<div class="small-box bg-red">
			  <div class="inner">
				  <h3><?=  Meals::find()->where(['status'=>  1])->count(); ?></h3>
				<p>Meals</p>
			  </div>
			  <div class="icon">
				<i class="ion ion-android-bar"></i>
			  </div>
			</div>
		</a>
      </div>
	<div class="col-lg-3 col-xs-6">
		<a href="<?= Yii::getAlias('@backendURL'); ?>/quick-foods">
			<!-- small box -->
			<div class="small-box bg-blue">
			  <div class="inner">
				  <h3><?=  QuickFoods::find()->where(['status'=>  1])->count(); ?></h3>
				<p>Quick Foods</p>
			  </div>
			  <div class="icon">
				<i class="ion ion-beer"></i>
			  </div>
			</div>
		</a>
      </div>
		<div class="col-lg-3 col-xs-6">
		<a href="<?= Yii::getAlias('@backendURL'); ?>/dishes">
			<!-- small box -->
			<div class="small-box bg-green">
			  <div class="inner">
				  <h3><?=  Dishes::find()->where(['status'=>  1])->count(); ?></h3>
				<p>Dishes</p>
			  </div>
			  <div class="icon">
				<i class="ion ion-archive"></i>
			  </div>
			</div>
		</a>
      </div>
    </div>
	  
    <!-- /.row -->
  </section>
  <!-- /.content -->