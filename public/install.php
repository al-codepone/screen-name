<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

$modelNames = array(
    'scrnnm\db\LoginModel',
    'scrnnm\db\UserModel',
    'scrnnm\db\VerifyEmailModel',
    'scrnnm\db\ResetPasswordModel');

foreach($modelNames as $modelName) {
    $model = ModelFactory::get($modelName);
    $model->install();
}

printf('Install successful. Visit the <a href="%s">home page</a>.', ROOT);
