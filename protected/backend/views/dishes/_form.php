<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Restaurants;
use common\models\Cuisines;
use common\models\QuickFoods;
use common\models\Meals;
use budyaga\cropper\Widget;
use yii\web\JsExpression;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Dishes */
/* @var $form yii\widgets\ActiveForm */
if(!empty($model->dish_images)){
	$selected_images = $model->dish_images;
}else{
	$selected_images[] = null;	
}
$videoPreviewData = [];
if(!empty($model->dish_video)){
	
	$ext = pathinfo($model->dish_video, PATHINFO_EXTENSION);
	$type = "";
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'chrome') === false){
		if(strtolower($ext) == "mov"){
			$type = 'type="video/quicktime"';
		}else{
			$type = 'type="video/'.$ext.'"';
		}
	}
	$videoPreviewData = ['<video width="400" controls>
	  <source src="'.$model->dish_video.'" '.$type.'>
	</video>'];
}
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(
		[
			'options' => [
				'id' => 'dishForm',
				'enctype'=>'multipart/form-data'
			],
		 	'enableClientValidation' => true,'enableAjaxValidation' => false
		]); ?>
    <div class="box-body">
		<div class="row">		
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
			</div>	
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?php
				$dataRestaurants = [];
				$dataRestaurants = ArrayHelper::map(Restaurants::find()->
				 asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all(),'user_id', 'name');    

				echo $form->field($model, 'user_id')->dropDownList($dataRestaurants,['prompt'=>'Select Restaurant']);
				?>
			</div>
		</div>

		<div class="row">		
			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
				<?php
				$dataCuisines = [];
				$listCusines = Cuisines::find()->asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all();
				$dataCuisines = ArrayHelper::map($listCusines,'id', 'name');    
				$optionsCusines = [];
				foreach($listCusines as $cusineDetails){
					$optionsCusines[$cusineDetails['id']] = ['data-img-src' => $cusineDetails['image']];
				}
				echo $form->field($model, 'cuisine_id', ['template' => '<label class="control-label">{label}</label><div class="loader">Loading...</div>{input}'])->dropDownList($dataCuisines,
										[
											'options' => $optionsCusines,
											'prompt'=>'Select Cuisine',
											'onchange'=>'$.post("'.Yii::$app->urlManager->createUrl('dishes/list-meals?id=').'"+$(this).val(),function( data ){
															 $( "select#dishes-meal_id" ).html( data );
															 $("select#dishes-meal_id").imagepicker({show_label: true});
															 $(".field-dishes-meal_id .thumbnails.image_picker_selector").css("width",parseInt($("select#dishes-meal_id option").length)*103);
															 $("#mealWrapper").css("display","");
														   });',
											'style' => 'display:none;'
										]);
				?>
			</div>				
		</div>		
		<div id="mealWrapper" class="row" style="<?php echo empty($model->cuisine_id) ? 'display : none;': ''; ?>">	
			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
				<?php
				$dataMeals = $optionsMeals = [];
				if(!empty($model->cuisine_id)){
					$listMeals = Meals::find()->where(['cuisine_id' => $model->cuisine_id,'status'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all();
					$dataMeals = ArrayHelper::map($listMeals,'id', 'name');    
					foreach($listMeals as $mealDetails){
						$optionsMeals[$mealDetails['id']] = ['data-img-src' => $mealDetails['image']];
					}
				}
				?>
				<?= $form->field($model, 'meal_id', ['template' => '<label class="control-label">{label}</label><div class="loader">Loading...</div>{input}'])->dropDownList($dataMeals,['options' => $optionsMeals,'prompt'=>'Select Meal','style' => 'display:none;']);?> 
			</div>
		</div>
		
		<div class="row">	
			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
				<?php
				$dataQuickFoods = $optionsQuickFoods = [];
				$listQuickFoods = QuickFoods::find()->asArray()->where(['status'=>1])->orderBy(['name'=>SORT_ASC])->all();
				$dataQuickFoods = ArrayHelper::map($listQuickFoods,'id', 'name');    
				foreach($listQuickFoods as $quickFoodDetails){
					$optionsQuickFoods[$quickFoodDetails['id']] = ['data-img-src' => $quickFoodDetails['image']];
				}
				echo $form->field($model, 'quick_food_id', ['template' => '<label class="control-label">{label}</label><div class="loader">Loading...</div>{input}'])->dropDownList($dataQuickFoods,['options' => $optionsQuickFoods,'prompt'=>'Select Quick Food','style' => 'display:none;']);
				?>
			</div>
		</div>
		
		<div class="row">	
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
			</div>		
			<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
				<?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
			</div>		
		</div>
		<div class="row">		
			<div class="col-lg-8 col-md-8 col-xs-12 col-sm-12">
				<?php echo $form->field($model, 'dish_images[]')->widget(FileInput::classname(), [
					'options' => [
						'accept' => 'image/*',
						'multiple' => true
					],
					'pluginOptions' => [
						'showUpload'=>false,
						'allowedFileExtensions'=>['jpg', 'png', 'jpeg'],
						'initialPreview'=>$selected_images,
						'overwriteInitial' => true,
						'autoReplace' => true,
						'initialPreviewAsData'=>true,
						'maxFileSize'=>5120,
					 	'maxFileCount' => 3
					],
				]); 
				?>
			</div>
		</div>
		<div class="row">		
			<div class="col-lg-8 col-md-8 col-xs-12 col-sm-12">
				<?php 
				echo $form->field($model, 'dish_video_duration')->hiddenInput(['value'=> 0])->label(false);
				echo $form->field($model, 'dish_video')->widget(FileInput::classname(), [
					'options' => [
						'accept' => 'video/*',
						'multiple' => true
					],
					'pluginOptions' => [
						'showUpload'=>false,
						'allowedFileExtensions'=>['mp4'],
						'maxFileSize'=>15360,
						'initialPreview'=>$videoPreviewData,
						'overwriteInitial' => true,
						'autoReplace' => true,
					],
				]); 
				?>
			</div>
		</div>
	</div>
    <div class="box-footer">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['dishes']), ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(
  "$(document).on('ready pjax:success', function() {  
	window.URL = window.URL || window.webkitURL;

	document.getElementById('dishes-dish_video').onchange = setFileInfo;

	function setFileInfo() {
	  var files = this.files;
	  var video = document.createElement('video');
	  video.preload = 'metadata';

	  video.onloadedmetadata = function() {
		window.URL.revokeObjectURL(video.src);
		var duration = video.duration;
		$('#dishes-dish_video_duration').val(duration);
	  }

	  video.src = URL.createObjectURL(files[0]);;
	}
	$('select#dishes-cuisine_id').imagepicker({show_label: true});
	$('.field-dishes-cuisine_id .thumbnails.image_picker_selector').css('width',parseInt($('select#dishes-cuisine_id option').length)*106);
	$('.field-dishes-cuisine_id .loader').css('display','none');
 	$('select#dishes-meal_id').imagepicker({show_label: true});
	$('.field-dishes-meal_id .thumbnails.image_picker_selector').css('width',parseInt($('select#dishes-meal_id option').length)*106);
	$('.field-dishes-meal_id .loader').css('display','none');
	$('select#dishes-quick_food_id').imagepicker({show_label: true});
	$('.field-dishes-quick_food_id .thumbnails.image_picker_selector').css('width',parseInt($('select#dishes-quick_food_id option').length)*106);
	$('.field-dishes-quick_food_id .loader').css('display','none');
});
");
?>