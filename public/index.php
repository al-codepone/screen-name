<?php

require 'const.php';
require 'vendor/autoload.php';

use vanilla\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$userModel = ModelFactory::get('vanilla\db\UserModel');
$loginModel = ModelFactory::get('vanilla\db\LoginModel');
$user = $loginModel->getActiveUser();

include 'src/vanilla/routes/' . route(array(
    null => 'home.php',
    'signup' => 'sign-up.php',
    'login' => 'login.php',
    'edit-account' => 'edit-account.php',
    'verify-email' => 'verify-email.php',
    'forgot-password' => 'forgot-password.php',
    'reset-password' => 'reset-password.php'));

$navItems = navItems($user);
include 'src/vanilla/html/template.php';

?>
