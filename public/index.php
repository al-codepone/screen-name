<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$userModel = ModelFactory::get('scrnnm\db\UserModel');
$user = $userModel->getActiveUser();

include SRC . 'scrnnm/routes/' . pc\route(array(
    null => 'home.php',
    'signup' => 'sign-up.php',
    'login' => 'login.php',
    'edit-account' => 'edit-account.php',
    'verify-email' => 'verify-email.php',
    'forgot-password' => 'forgot-password.php',
    'reset-password' => 'reset-password.php'));

$t_nav_items = nav_items($user);
include SRC . 'scrnnm/html/template.php';
