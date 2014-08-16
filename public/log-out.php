<?php

require 'const.php';
require 'vendor/autoload.php';

use vanilla\db\ModelFactory;

session_name(SESSION_NAME);
session_start();

$loginModel = ModelFactory::get('vanilla\db\LoginModel');
$loginModel->logOut();
header('Location:' . ROOT);
exit();

?>
