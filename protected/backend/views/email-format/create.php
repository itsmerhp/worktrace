<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EmailFormat */

$this->title = 'Create Email Format';
$this->params['breadcrumbs'][] = ['label' => 'Email Formats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

        
