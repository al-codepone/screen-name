<?php

use scrnnm\db\ModelFactory;

$verifyEmailModel = ModelFactory::get('scrnnm\db\VerifyEmailModel');
$data = $verifyEmailModel->getToken($_GET['id'], $_GET['token']);

if($data) {
    $email = $data['data'];
    $content = ($error = $userModel->updateEmail($data['user_id'], $email))
        ? $error
        : 'Thank you, your email has been verified.';

    $verifyEmailModel->deleteToken($data['token_id']);
}
else {
    $content = 'Invalid verification.';
}

$head = c\title('Verify Email');
