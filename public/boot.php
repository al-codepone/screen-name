<?php

require 'C:/xampp/htdocs/screen-name/const.php';
require 'C:/xampp/htdocs/screen-name/vendor/autoload.php';

set_exception_handler(function($e) {
    if($e instanceof pjsql\DatabaseException) {
        die('database error');
    }
    else {
        throw $e;
    }
});
