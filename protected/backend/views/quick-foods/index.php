<?php

use yii\helpers\Html;
use yii\grid\GridView;
use Yii;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Cuisines */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quick Foods';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
	<h1>
		<?= Html::encode($this->title) ?>
		<!-- <small>Preview</small> -->	
	</h1>
	<p style="margin: 10px 0 0">
		<?= Html::a('Create Quick Food', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createUrl(['quick-foods']), ['class' => 'btn btn-primary']) ?>
	</p>
	
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

					//'id',
					'name',
					[
						'attribute' => 'image',
						'format' => 'html',    
						'value' => function ($data) {
							return Html::img($data->image,
								['width' => '70px']);
						},
					],
					[
						'attribute' => 'status',
						'filter' => Yii::$app->params['STATUS_SELECT'],
						'format' => 'raw',
						'value'  => function($data){
							$icon = ($data->status == '1') ? '<span class="glyphicon glyphicon-ok-circle"></span>' : '<span class="glyphicon glyphicon-remove-circle"></span>';
							return Html::a($icon, Yii::$app->urlManager->createUrl(['quick-foods/active','id' => $data->id]), 
								[
									'title' => ($data->status == '1') ? Yii::t('app', 'Active') : Yii::t('app', 'Inactive'),
									'class' => 'align-center',
									//'data-pjax'          => '1',
									'data-toggle-active' => $data->id
								]
							);
							// return Yii::$app->params['STATUS_SELECT'][$data->status];
						}

					],
					[
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'template' => '{update}',
                    ]
					//'created_at',
					// 'updated_at',
					//['class' => 'yii\grid\ActionColumn'],
				],
			]); ?>
		</div>
	</div>
</section>