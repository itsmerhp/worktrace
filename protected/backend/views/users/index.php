<?php

use yii\helpers\Html;
use yii\grid\GridView;
use Yii;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Users */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
	<h1>
		<?= Html::encode($this->title) ?>
		<!-- <small>Preview</small> -->	
	</h1>
	<p style="margin: 10px 0 0">
		<?= Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createUrl(['users']), ['class' => 'btn btn-primary']) ?>
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

					//'user_id',
					//'role_id',
					//'user_type',
					[
						'attribute' => 'user_type',
						'filter' => Yii::$app->params['USER_TYPE_VALUES'],
						//'format' => 'raw',
						'value'  => function($data){
							return Yii::$app->params['USER_TYPE_VALUES'][$data->user_type];
						}

					],
					'name',
					'email:email',
					[
						'attribute' => 'profile_pic',
						'format' => 'html',    
						'value' => function ($data) {
							return Html::img(!empty($data->profile_pic) ? $data->profile_pic : Yii::getAlias('@host').'/uploads/no-image.png',['width' => '70px']);
						},
					],
					[
						'attribute' => 'status',
						'filter' => Yii::$app->params['STATUS_SELECT'],
						'format' => 'raw',
						'value'  => function($data){
							$icon = ($data->status == '1') ? '<span class="glyphicon glyphicon-ok-circle"></span>' : '<span class="glyphicon glyphicon-remove-circle"></span>';
							return Html::a($icon, Yii::$app->urlManager->createUrl(['users/active','id' => $data->user_id]), 
								[
									'title' => ($data->status == '1') ? Yii::t('app', 'Active') : Yii::t('app', 'Inactive'),
									'class' => 'align-center',
									//'data-pjax'          => '1',
									'data-toggle-active' => $data->user_id
								]
							);
							// return Yii::$app->params['STATUS_SELECT'][$data->status];
						}

					],
					[
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'template' => '{view}',
                    ]
				],
			]); ?>
		</div>
	</div>
</section>
