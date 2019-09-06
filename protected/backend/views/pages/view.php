<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Pages */

$this->title = $model->page_name;
$this->params['breadcrumbs'][] = ['label' => 'Pages', 'url' => ['index']];
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
    <li><a href="<?php echo Yii::getAlias('@backendURL'); ?>/pages">CMS pages</a></li>
    <li class="active"><a href="javascript:void(0)"><?= $model->page_name; ?></a></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <?= DetailView::widget([
        'model' => $model,
            'attributes' => [
                // 'id',
                'page_name',
                'page_content:ntext',
                // 'status',
                [
                    'attribute' => 'status',
                    'value'  => Yii::$app->params['STATUS_SELECT'][$model->status],
                ],
                // 'created_at',
                // 'updated_at',
            ],
        ]) ?>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</section>