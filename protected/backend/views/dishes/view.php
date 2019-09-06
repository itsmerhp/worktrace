<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use api\components\CommonApiHelper;

/* @var $this yii\web\View */
/* @var $model common\models\DIshes */

$this->title = "User : ".$model->title;
$this->params['breadcrumbs'][] = ['label' => 'Dishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content-header">
    <h1>
        <?= Html::encode($this->title) ?>
	</h1>
    
    <ol class="breadcrumb">
        <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li><a href="<?php echo Yii::getAlias('@backendURL'); ?>/dishes">Dishes</a></li>
        <li><a href="javascript:void(0)"><?= $this->title; ?></a></li>
    </ol>
</section>
<?php
	$dishImages = $dishVideo = "";
	if(!empty($model->dishMedia)){
		foreach($model->dishMedia as $dishMedia){
			if($dishMedia->type == 1){
				$dishImages .= "<img class='dishImage' src='".$dishMedia->url."' width='70px'/>";
			}else if($dishMedia->type == 2){
				//$dishVideo .= '<video width="320" height="240" src="'.$dishMedia->url.'"></video>';
				$ext = pathinfo($dishMedia->url, PATHINFO_EXTENSION);
				$type = "";
				if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'chrome') === false){
					if(strtolower($ext) == "mov"){
						$type = 'type="video/quicktime"';
					}else{
						$type = 'type="video/'.$ext.'"';
					}
				}
				$dishVideo .= '<video width="400" controls>
				  <source src="'.$dishMedia->url.'" '.$type.'>
				</video>';
			}
		}
	}	
?>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body table-responsive">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					//'id',
					'title',
					[
						'attribute' => 'price',
						'value'  => (isset($model->user->restaurantDetails->country) ? $model->user->restaurantDetails->country->currency_code.'('.$model->user->restaurantDetails->country->currency_symbol.') ' : '').$model->price
					],
					'description:ntext',
					[
						'attribute' => 'user_id',
						'value'  => isset($model->user) ? $model->user->name : ''
					],
					[
						'attribute' => 'cuisine_name',
						'value'  => isset($model->cuisine->name) ? $model->cuisine->name : ''
					],
					[
						'attribute' => 'meal_name',
						'value'  => isset($model->meal->name) ? $model->meal->name : ''
					],
					[
						'attribute' => 'quick_food_name',
						'value'  => isset($model->quickFood) ? $model->quickFood->name : '',
						'visible' => isset($model->quickFood) ? true : false
					],
					[
						'attribute' => 'Image(s)',
						'value'  => !empty($dishImages) ? $dishImages : "",
						'format' => 'html',
						'visible' => !empty($dishImages) ? true : false
					],
					[
						'attribute' => 'Video',
						'value'  => !empty($dishVideo) ? $dishVideo : "",
						'format' => 'raw',
						'visible' => !empty($dishVideo) ? true : false
					],
					[
						'attribute' => 'Dish Ratings',
						'value'  => CommonApiHelper::displayStartRating(round($model->avg_dish_rating),8),
						'format' => 'html'
					],
					[
						'attribute' => 'Quality Ratings',
						'value'  => CommonApiHelper::displayStartRating(round($model->avg_quality_rating)),
						'format' => 'html'
					],
					[
						'attribute' => 'Appearance Ratings',
						'value'  => CommonApiHelper::displayStartRating(round($model->avg_appearance_rating)),
						'format' => 'html'
					],
					[
						'attribute' => 'Total Ratings',
						'value'  => $model->total_ratings
					],
					[
						'attribute' => 'status',
						'value'  => Yii::$app->params['STATUS_SELECT'][$model->status]
					]					
				],
			]) ?>
		</div>
	</div>
</section>
