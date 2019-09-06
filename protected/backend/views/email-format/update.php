<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EmailFormat */

$this->title = 'Update Email Format: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Email Formats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>