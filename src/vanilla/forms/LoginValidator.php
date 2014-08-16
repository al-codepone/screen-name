<?php

namespace vanilla\forms;

use cityphp\forms\FormValidator;

class LoginValidator extends FormValidator {
    public function __construct() {
        parent::__construct(
            array(
                'username',
                'password',
                'remember_me' => false),
            array(
                'remember_me'));
    }
}

?>
