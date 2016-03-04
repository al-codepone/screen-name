<?php

use scrnnm\model\ModelFactory;

$verify_model = ModelFactory::get('scrnnm\model\VerifyEmailModel');
$data = $verify_model->getToken($_GET['id'], $_GET['token']);

if($data) {
    $email = $data['data'];
    $t_content = ($error = $user_model->updateEmail($data['user_id'], $email))
        ? $error
        : 'Thank you, your email has been verified.';

    $verify_model->deleteToken($data['token_id']);
}
else {
    $t_content = 'Invalid verification.';
}

$t_head = c\title('Verify Email');
