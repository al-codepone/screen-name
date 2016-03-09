<?php

require 'boot.php';

use scrnnm\model\ModelFactory;

session_name(SESSION_NAME);
session_start();

$user_model = ModelFactory::get('scrnnm\model\User');
$user = $user_model->getActiveUser();

include SRC . 'scrnnm/route/' . pc\route(array(
    null => 'home.php',
    'signup' => 'sign-up.php',
    'login.php',
    'edit-account.php',
    'verify-email.php',
    'forgot-password.php',
    'reset-password.php'));

$t_nav_items = nav_items($user);
include SRC . 'scrnnm/html/template.php';
