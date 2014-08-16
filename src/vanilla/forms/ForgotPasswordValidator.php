<?php

namespace vanilla\forms;

use cityphp\forms\FormValidator;

class ForgotPasswordValidator extends FormValidator {
    public function __construct() {
        parent::__construct(array('email'));
    }

    protected function validate_email($value) {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email';
        }
    }
}

?>
