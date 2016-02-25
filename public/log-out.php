<?php

require 'boot.php';

use scrnnm\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$userModel = ModelFactory::get('scrnnm\db\UserModel');
$userModel->logOut();
header('Location:' . ROOT);
exit();
