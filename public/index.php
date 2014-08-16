<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$userModel = ModelFactory::get('scrnnm\db\UserModel');
$loginModel = ModelFactory::get('scrnnm\db\LoginModel');
$user = $loginModel->getActiveUser();

include '../src/scrnnm/routes/' . route(array(
    null => 'home.php',
    'signup' => 'sign-up.php',
    'login' => 'login.php',
    'edit-account' => 'edit-account.php',
    'verify-email' => 'verify-email.php',
    'forgot-password' => 'forgot-password.php',
    'reset-password' => 'reset-password.php'));

$navItems = navItems($user);
include '../src/scrnnm/html/template.php';
