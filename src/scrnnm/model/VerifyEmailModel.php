<?php

namespace scrnnm\model;

use pjsql\DatabaseHandle;

class VerifyEmailModel extends TokenModel {
    public function __construct(DatabaseHandle $databaseHandle) {
        parent::__construct($databaseHandle, TABLE_VERIFY_EMAIL_TOKENS, TTL_VERIFY_EMAIL);
    }

    public function createToken($user_id, $username, $email) {
        $token = \pc\sha1_token();
        $subject = 'Verify Your Email';
        $additionalHeaders = sprintf("From: %s\r\n", EMAIL_FROM);
        $message = sprintf("%s,\n\nClick the link to verify your email:\n\n%s%s%d/%s",
            $username, SITE, VERIFY_EMAIL, $user_id, $token);

        parent::createToken($user_id, $token, $email);
        email($email, $subject, $message, $additionalHeaders);
    }
}
