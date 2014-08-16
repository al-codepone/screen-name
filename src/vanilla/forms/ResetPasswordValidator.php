<?php

namespace vanilla\forms;

use cityphp\forms\FormValidator;

class ResetPasswordValidator extends FormValidator {
    public function __construct() {
        parent::__construct(array(
            'password',
            'confirm_password'));
    }

    protected function validate_password($value) {
        return validatePassword($value, 'New password');
    }

    protected function validate_confirm_password($value) {
        return validatePassword($value, 'Confirm new password');
    }

    protected function validateMore($values) {
        if($values['password'] != $values['confirm_password']) {
            return "Passwords didn't match";
        }
    }
}

?>
