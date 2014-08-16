<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$loginModel = ModelFactory::get('scrnnm\db\LoginModel');
$loginModel->logOut();
header('Location:' . ROOT);
exit();
