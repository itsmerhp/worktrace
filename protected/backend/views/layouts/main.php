<?php
use backend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?=  Yii::$app->params['siteName']; ?> | <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
        $this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css");
        $this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css");
    ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>    
    <?php if(isset(Yii::$app->user->identity)){ ?>        
        <div class="wrapper">            
            <header class="main-header">                
                <!-- Logo -->
                <a href="<?= Yii::getAlias('@backendURL'); ?>" class="logo">
                  <!-- mini logo for sidebar mini 50x50 pixels -->
                  <span class="logo-mini"><b><?=Yii::t('app', \Yii::$app->params['siteShortName']);?></b></span>
                  <!-- logo for regular state and mobile devices -->
                  <span class="logo-lg"><?=Yii::t('app', \Yii::$app->params['siteName']);?></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                  <!-- Sidebar toggle button-->
                  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                  </a>

                  <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">                      
                      <!-- User Account: style can be found in dropdown.less -->
                      <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                          <!--<img src="<?= Yii::getAlias("@backendURL"); ?>/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                          <span class="">
							  <?php echo "<b>".Yii::$app->user->identity->name."</b>"; ?>&nbsp;&nbsp;
							  <i class="fa fa-chevron-down"></i>
							</span>
                        </a>
                        <ul class="dropdown-menu">
                          <!-- User image -->
                          <!--<li class="user-header">
                            <img src="<?= Yii::getAlias("@backendURL"); ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                            <p>
                                <?= Yii::$app->user->identity->name; ?>
                            </p>
                          </li>-->
                          <!-- Menu Footer-->
                          <li class="user-footer">
                            <div class="pull-left">
                              <a href="<?= \Yii::$app->urlManager->createUrl(['site/change-password']); ?>" class="btn btn-default btn-flat">Change Password</a>
                            </div>
                            <div class="pull-right">
                              <a href="<?= \Yii::$app->urlManager->createUrl(['site/logout']); ?>" class="btn btn-default btn-flat"><?= Yii::t('app', 'Logout');?></a>
                            </div>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </div>
                </nav>
              </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
              <!-- sidebar: style can be found in sidebar.less -->
              <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel" style="display:none">
                  <div class="pull-left image">
                    <img src="<?= Yii::getAlias("@backendURL"); ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                  </div>
                  <div class="pull-left info">
                    <p><?= Yii::$app->user->identity->name; ?></p>
                    <a href="<?= \Yii::$app->urlManager->createUrl(['site/logout']); ?>">
                        <i class="fa fa-circle text-success"></i>
                        <?= Yii::t('app', 'Logout');?>
                    </a>
                  </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php //p(Yii::$app->controller->id) ?>
                <?php //p(Yii::$app->controller->action->id) ?>
                <ul class="sidebar-menu">
                  <li class="<?php echo (Yii::$app->controller->id == 'site') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>">
                      <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                  </li>
                  <li class="<?php echo (Yii::$app->controller->id == 'restaurant-otp') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/restaurant-otp">
                      <i class="fa fa-envelope-o"></i> <span>Restaurant OTP</span>
                    </a>
                  </li>
                  <li class="<?php echo (Yii::$app->controller->id == 'users') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/users">
                      <i class="fa fa-users"></i> <span>Users</span>
                    </a>
                  </li>
				  <li class="<?php echo (Yii::$app->controller->id == 'cuisines') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/cuisines">
                      <i class="fa fa-cutlery"></i> <span>Cuisines</span>
                    </a>
                  </li>
				  <li class="<?php echo (Yii::$app->controller->id == 'meals') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/meals">
                      <i class="fa fa-cutlery"></i> <span>Meals</span>
                    </a>
                  </li>
				  <li class="<?php echo (Yii::$app->controller->id == 'quick-foods') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/quick-foods">
                      <i class="fa fa-cutlery"></i> <span>Quick Foods</span>
                    </a>
                  </li>
				  <li class="<?php echo (Yii::$app->controller->id == 'dishes') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/dishes">
                      <i class="fa fa-cutlery"></i> <span>Dishes</span>
                    </a>
                  </li>
                  <!--<li class="<?php echo (Yii::$app->controller->id == 'users') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/users">
                      <i class="fa fa-users"></i> <span>Users</span>
                    </a>
                  </li>
                  <li class="<?php echo (Yii::$app->controller->id == 'email-format') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/email-format">
                      <i class="fa fa-envelope"></i> <span>Email Format</span>
                    </a>
                  </li>
                  <li class="<?php echo (Yii::$app->controller->id == 'pages') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/pages">
                      <i class="fa fa-copy"></i> <span>CMS pages</span>
                    </a>
                  </li>
                  <li class="<?php echo (Yii::$app->controller->id == 'publication' || Yii::$app->controller->id == 'publisher-post' || Yii::$app->controller->id == 'articles') ? 'active' : ''?> treeview">
                    <a href="<?= Yii::getAlias('@backendURL'); ?>/publication">
                      <i class="fa fa-edit"></i> <span>Publications</span>
                    </a>
                  </li>
                  <!--<li class="treeview">
                    <a href="#">
                      <i class="fa fa-pie-chart"></i>
                      <span>Charts</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="pages/charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a></li>
                      <li><a href="pages/charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a></li>
                      <li><a href="pages/charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a></li>
                      <li><a href="pages/charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a></li>
                    </ul>
                  </li>-->
                </ul>
              </section>
              <!-- /.sidebar -->
            </aside>
            
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="flash_message">
                    <?php include_once 'flash_message.php'; ?>
                </div>

                <?= $content ?>   
            </div>
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                </div>
                <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="<?= Yii::getAlias('@backendURL'); ?>"><?php echo \Yii::$app->params['siteName']; ?></a>.</strong> All rights
                reserved.
            </footer>
        </div>        
        <div class="control-sidebar-bg"></div>
    <?php }else{ 
        header('Location : /site/login');
    } ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
