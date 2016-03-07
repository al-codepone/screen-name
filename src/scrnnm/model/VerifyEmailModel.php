<?php

namespace scrnnm\model;

use pjsql\DatabaseHandle;

class VerifyEmailModel extends TokenModel {
    public function __construct(DatabaseHandle $databaseHandle) {
        parent::__construct($databaseHandle, 'tverify_email_token', TTL_VERIFY_EMAIL);
    }

    public function create($user_id, $username, $email) {
        $token = \pc\sha1_token();
        $subject = 'Verify Your Email';
        $additional_headers = sprintf("From: %s\r\n", EMAIL_FROM);
        $message = sprintf("%s,\n\nClick the link to verify your email:\n\n%s%s%d/%s",
            $username, SITE, VERIFY_EMAIL, $user_id, $token);

        parent::create($user_id, $token, $email);
        email($email, $subject, $message, $additional_headers);
    }
}
