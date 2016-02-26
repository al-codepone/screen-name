<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$user_model = ModelFactory::get('scrnnm\db\UserModel');
$user_model->logOut();
header('Location:' . ROOT);
exit();
