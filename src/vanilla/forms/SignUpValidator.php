<?php


namespace vanilla\forms;

use cityphp\forms\FormValidator;

class SignUpValidator extends FormValidator {
    public function __construct() {
        parent::__construct(array(
            'username',
            'email',
            'password',
            'confirm_password'));
    }

    protected function validate_username($value) {
        return validateUsername($value);
    }

    protected function validate_email($value) {
        if($value != '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email';
        }
    }

    protected function validate_password($value) {
        return validatePassword($value, 'Password');
    }

    protected function validate_confirm_password($value) {
        return validatePassword($value, 'Confirm password');
    }

    protected function validateMore($values) {
        if($values['password'] != $values['confirm_password']) {
            return "Passwords didn't match";
        }
    }
}

?>
