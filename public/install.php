<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

$model_names = array(
    'scrnnm\db\UserModel',
    'scrnnm\db\VerifyEmailModel',
    'scrnnm\db\ResetPasswordModel');

foreach($model_names as $model_name) {
    $model = ModelFactory::get($model_name);
    $model->install();
}

printf('Install successful. Visit the <a href="%s">home page</a>.', ROOT);
