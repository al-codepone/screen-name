<?php

namespace scrnnm\validator;

class LoginValidator extends \bbook\FormValidator {
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
