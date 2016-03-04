<?php

require 'boot.php';

use scrnnm\model\ModelFactory;

session_name(SESSION_NAME);
session_start();

$user_model = ModelFactory::get('scrnnm\model\UserModel');
$user_model->logOut();
header('Location:' . ROOT);
exit();
