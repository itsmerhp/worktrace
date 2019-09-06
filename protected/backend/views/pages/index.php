<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pages';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
<h1>
    <?= Html::encode($this->title) ?>
    <!-- <small>Preview</small> -->
    <?php //echo Html::a('Create Pages', ['create'], ['class' => 'btn btn-success']) ?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="javascript:void(0)"><?= $this->title; ?></a></li>
  </ol>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-body table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'page_name',
            // 'page_content:ntext',
            // 'status',
            [
                    'attribute' => 'status',
                    'filter' => Yii::$app->params['STATUS_SELECT'],
                    'value'  => function($data){
                        return Yii::$app->params['STATUS_SELECT'][$data->status];
                    }

                ],
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn','template' => '{view} {update}'],
        ],
    ]); ?>
</div><!-- /.box-body -->
        <div class="box-footer clearfix">
            <!-- other markup -->
        </div><!-- /.box-footer-->
    </div>
</section>
<!-- END : Main content -->
