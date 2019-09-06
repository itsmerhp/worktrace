<?php

use yii\helpers\Html;
use yii\grid\GridView;
use Yii;
use common\models\Cuisines;
use common\models\Meals;
use common\models\QuickFoods;
use common\models\Restaurants;
use yii\helpers\ArrayHelper;
use api\components\CommonApiHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Dishes */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dishes';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
	<h1>
		<?= Html::encode($this->title) ?>
		<!-- <small>Preview</small> -->	
	</h1>
	<p style="margin: 10px 0 0">
		<?php echo Html::a('Create Dish', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('app', 'Reset'), Yii::$app->urlManager->createUrl(['dishes']), ['class' => 'btn btn-primary']) ?>
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
					'title',
					// 'description:ntext',
					[
						'attribute' => 'price',
						'format' => 'raw',    
						'value' => function ($data) {
							return (isset($data->user->restaurantDetails->country) ? $data->user->restaurantDetails->country->currency_code.'('.$data->user->restaurantDetails->country->currency_symbol.') ' : '').$data->price;
						},
					],
					[
						'attribute' => 'user_name',
						'format' => 'raw',    
						'value' => function ($data) {
							return isset($data->user) ? $data->user->name : '';
						},
					],
					[
						'attribute' => 'cuisine_name',
						/*'filter' => ArrayHelper::map(Cuisines::find()->
						 asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all(),'id', 'name'),*/
						'format' => 'raw',
						'value'  => function($data){
							return $data->cuisine->name;
						}

					],
					[
						'attribute' => 'meal_name',
						/*'filter' => ArrayHelper::map(Meals::find()->
						 asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all(),'id', 'name'),*/
						'format' => 'raw',
						'value'  => function($data){
							return $data->meal->name;
						}

					],
					/*[
						'attribute' => 'quick_food_name',
						//'filter' => ArrayHelper::map(QuickFoods::find()->asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all(),'id', 'name'),
						'format' => 'raw',
						'value'  => function($data){
							return isset($data->quickFood->name) ? $data->quickFood->name : '';
						}

					],*/
					[
						'attribute' => 'Image',
						'format' => 'html',    
						'value' => function ($data) {
							return Html::img(!empty($data->dishPrimaryImage) ? $data->dishPrimaryImage->url : Yii::getAlias('@host').'/uploads/no-image.png',['width' => '70px']);
						},
					],
					[
						'contentOptions' => ['style' => 'width: 160px;'],
						'attribute' => 'avg_dish_rating',
						'format' => 'html',    
						'value' => function ($data) {
							return CommonApiHelper::displayStartRating(round($data->avg_dish_rating),8);
						},
					],
					[
						'contentOptions' => ['style' => 'width: 80px;'],
						'attribute' => 'avg_quality_rating',
						'format' => 'html',    
						'value' => function ($data) {
							return CommonApiHelper::displayStartRating(round($data->avg_quality_rating));
						},
					],
					[
						'contentOptions' => ['style' => 'width: 80px;'],
						'attribute' => 'avg_appearance_rating',
						'format' => 'html',    
						'value' => function ($data) {
							return CommonApiHelper::displayStartRating(round($data->avg_appearance_rating));
						},
					],
					//'avg_dish_rating',
					//'avg_quality_rating',
					//'avg_appearance_rating',
					'total_ratings',
					[
						'attribute' => 'status',
						'filter' => Yii::$app->params['STATUS_SELECT'],
						'format' => 'raw',
						'value'  => function($data){
							$icon = ($data->status == '1') ? '<span class="glyphicon glyphicon-ok-circle"></span>' : '<span class="glyphicon glyphicon-remove-circle"></span>';
							return Html::a($icon, Yii::$app->urlManager->createUrl(['dishes/active','id' => $data->id]), 
								[
									'title' => ($data->status == '1') ? Yii::t('app', 'Active') : Yii::t('app', 'Inactive'),
									//'class' => 'align-center',
									//'data-pjax'          => '1',
									'data-toggle-active' => $data->id
								]
							);
							// return Yii::$app->params['STATUS_SELECT'][$data->status];
						}

					],
					//'',
					//'price',
					// 'status',
					// 'created_at',
					// 'updated_at',

					
					[
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'template' => '{update} {view}',
                    ]
				],
			]); ?>
		</div>
	</div>
</section>
