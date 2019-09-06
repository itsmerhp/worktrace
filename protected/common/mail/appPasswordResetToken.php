<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Url::to('@siteRoot/site/reset-app-password?token=' . $user->password_reset_token, true);
?>

Hello <?= Html::encode($user->username) ?>,<br/><br/>

Follow this link below to reset your password:<br/>

<?= Html::a('Please, click here to reset your password.', $resetLink) ?><br/><br/>

Regards,<br/>
Crityk Team
