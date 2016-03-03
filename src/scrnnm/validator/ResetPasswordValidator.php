<?php

namespace scrnnm\validator;

class ResetPasswordValidator extends \bbook\FormValidator {
    public function __construct() {
        parent::__construct(array(
            'password',
            'confirm_password'));
    }

    protected function validate_password($value) {
        return validate_password($value, 'New password');
    }

    protected function validate_confirm_password($value) {
        return validate_password($value, 'Confirm new password');
    }

    protected function more($values) {
        if($values['password'] != $values['confirm_password']) {
            return "Passwords didn't match";
        }
    }
}
