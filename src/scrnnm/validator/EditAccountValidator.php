<?php

namespace scrnnm\validator;

class EditAccountValidator extends \bbook\FormValidator {
    public function __construct() {
        parent::__construct(array(
            'delete_flag' => false,
            'username',
            'email',
            'password',
            'confirm_password',
            'current_password'));
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
        if($value != '' && !is_password($value)) {
            return invalid_password('New password');
        }
    }

    protected function validate_confirm_password($value) {
        if($value != '' && !is_password($value)) {
            return invalid_password('Confirm new password');
        }
    }

    protected function more($values) {
        if($values['password'] != $values['confirm_password']) {
            return "New passwords didn't match";
        }
    }
}
