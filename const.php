<?php

define('SITE', 'localhost');
define('ROOT', '/cityphp/sample-applications/vanilla/deploy/');
define('CSS', ROOT . 'css/');
define('JS', ROOT . 'js/');
define('SIGN_UP', ROOT . 'signup');
define('LOGIN', ROOT . 'login');
define('LOG_OUT', ROOT . 'logout');
define('EDIT_ACCOUNT', ROOT . 'edit-account');
define('VERIFY_EMAIL', ROOT . 'verify-email/');
define('FORGOT_PASSWORD', ROOT . 'forgot-password');
define('RESET_PASSWORD', ROOT . 'reset-password/');

define('MYSQL_HOST', 'localhost');
define('MYSQL_USERNAME', 'big');
define('MYSQL_PASSWORD', 'tree');
define('MYSQL_DBNAME', 'myfirstdb');
define('MYSQL_DEBUG', false);

define('TABLE_USERS', 'users');
define('TABLE_PERSISTENT_LOGIN_TOKENS', 'persistent_login_tokens');
define('TABLE_VERIFY_EMAIL_TOKENS', 'verify_email_tokens');
define('TABLE_RESET_PASSWORD_TOKENS', 'reset_password_tokens');

define('SESSION_NAME', 'VSID01');
define('SESSION_USER_ID', 'user_id');
define('SESSION_USERNAME', 'username');

define('COOKIE_PERSISTENT_LOGIN', 'persistent_login');

define('EMAIL_FROM', 'noreply@mysite.com');
define('EMAIL_IS_SEND', false);
define('EMAIL_IS_LOG', true);
define('EMAIL_LOG_FILE', 'C:/wamp/vanilla-log.txt');

define('TTL_PERSISTENT_LOGIN', 7);
define('TTL_VERIFY_EMAIL', 30);
define('TTL_RESET_PASSWORD', 1);

define('BCRYPT_COST', 10);

?>
