<?php

namespace scrnnm\model;

use pjsql\DatabaseHandle;

class ResetPasswordModel extends TokenModel {
    public function __construct(DatabaseHandle $databaseHandle) {
        parent::__construct($databaseHandle, TABLE_RESET_PASSWORD_TOKENS, TTL_RESET_PASSWORD);
    }

    public function createToken($email) {
        $user_model = ModelFactory::get('scrnnm\model\UserModel');
        $user_data = $user_model->getUserWithEmail($email);

        if($user_data) {
            $token = \pc\sha1_token();
            $subject = 'Reset Your Password';
            $additionalHeaders = sprintf("From: %s\r\n", EMAIL_FROM);
            $message = sprintf("%s,\n\nUse this link to reset your password:\n\n%s%s%d/%s",
                $user_data['username'], SITE, RESET_PASSWORD, $user_data['user_id'], $token);

            parent::createToken($user_data['user_id'], $token);
            email($email, $subject, $message, $additionalHeaders);
        }
    }

    public function getToken($user_id, $token) {
        return parent::getToken($user_id, $token);
    }

    public function deleteToken($tokenID) {
        parent::deleteToken($tokenID);
    }
}
