<?php

namespace scrnnm\model;

use pjsql\DatabaseHandle;

class ResetPasswordModel extends TokenModel {
    public function __construct(DatabaseHandle $databaseHandle) {
        parent::__construct($databaseHandle, 'treset_password_token', TTL_RESET_PASSWORD);
    }

    public function create($email) {
        $user_model = ModelFactory::get('scrnnm\model\UserModel');
        $user_data = $user_model->getUserWithEmail($email);

        if($user_data) {
            $token = \pc\sha1_token();
            $subject = 'Reset Your Password';
            $additional_headers = sprintf("From: %s\r\n", EMAIL_FROM);
            $message = sprintf("%s,\n\nUse this link to reset your password:\n\n%s%s%d/%s",
                $user_data['username'], SITE, RESET_PASSWORD, $user_data['user_id'], $token);

            parent::create($user_data['user_id'], $token);
            email($email, $subject, $message, $additional_headers);
        }
    }
}
