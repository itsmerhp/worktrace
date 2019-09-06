<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EmailFormatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Formats';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
  <h1>
    <?= Html::encode($this->title) ?>
    <!-- <small>Preview</small> -->
  </h1>
    <?php //echo Html::a('Create Users', ['create'], ['class' => 'btn btn-success']) ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="javascript:void(0)"><?= $this->title; ?></a></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => ['options' => ['class' => 'pagination pull-right']],
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    /*[
                        'attribute' => 'id',
                        'filter'    => false,
                    ],*/
                    'title',
                    'subject',
                    'body:html',
                    [
                        'attribute' => 'status',
                        'filter' => Yii::$app->params['STATUS_SELECT'],
                        'value'  => function($data){
                            return Yii::$app->params['STATUS_SELECT'][$data->status];
                        }

                    ],
                    // 'created_at',
                    // 'updated_at',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                    ],
                ],
            ]); ?>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
            <!-- other markup -->
        </div><!-- /.box-footer-->
    </div>
</section>
<!-- END : Main content -->
