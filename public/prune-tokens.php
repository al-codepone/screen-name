<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

$modelNames = array(
    'scrnnm\db\LoginModel',
    'scrnnm\db\VerifyEmailModel',
    'scrnnm\db\ResetPasswordModel');

foreach($modelNames as $modelName) {
    $model = ModelFactory::get($modelName);
    $model->prune();
}

echo 'prune successful';
