<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

// $resetLink = Url::to('@siteRoot/site/reset-app-password?token=' . $user->password_reset_token, true);
?>

Hello <?= Html::encode($user->username) ?>,<br/><br/>

Welcome to Crityk.<br>

Kindly, let us know your thoughts on <a href="mailto:support@crityk.com">support@crityk.com</a>

<br/><br/>

Regards,<br/>
Crityk Team