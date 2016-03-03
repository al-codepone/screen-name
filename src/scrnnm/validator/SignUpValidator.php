<?php

namespace scrnnm\validator;

class SignUpValidator extends \bbook\FormValidator {
    public function __construct() {
        parent::__construct(array(
            'username',
            'email',
            'password',
            'confirm_password'));
    }

    protected function validate_username($value) {
        return validate_username($value);
    }

    protected function validate_email($value) {
        if($value != '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email';
        }
    }

    protected function validate_password($value) {
        return validate_password($value, 'Password');
    }

    protected function validate_confirm_password($value) {
        return validate_password($value, 'Confirm password');
    }

    protected function more($values) {
        if($values['password'] != $values['confirm_password']) {
            return "Passwords didn't match";
        }
    }
}
