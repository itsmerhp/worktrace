<?php
use yii\bootstrap\Alert;
    if($flash = Yii::$app->session->getFlash('success')){
            //echo Alert::widget(['options' => ['class'=> 'alert alert-success'], 'body'=> $flash]);
        ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Success</h4>
            <?= $flash ?>
        </div>
<?php
    } 
    if($flash = Yii::$app->session->getFlash('fail')){
            //echo Alert::widget(['options' => ['class'=> 'alert alert-danger'], 'body'=> $flash]);
        ?>
        <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Fail</h4>
            <?= $flash ?>
        </div>
<?php
    } 
?>