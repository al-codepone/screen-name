<?php

use scrnnm\db\ModelFactory;

$resetPasswordModel = ModelFactory::get('scrnnm\db\ResetPasswordModel');
$tokenData = $resetPasswordModel->getToken($_GET['id'], $_GET['token']);

if($tokenData) {
    $validator = new scrnnm\validator\ResetPasswordValidator();

    if(list($formData, $errors) = $validator->validate()) {
        if($errors) {
            $content = reset_password($formData, $errors);
        }
        else if($error = $userModel->updatePassword($tokenData['user_id'], $formData)) {
            $content = reset_password($formData, $error);
        }
        else {
            $resetPasswordModel->deleteToken($tokenData['token_id']);
            $content = 'Your password was successfully reset.';
        }
    }
    else {
        $autofocus = autofocus('password');
        $content = reset_password($validator->values());
    }
}
else {
    $content = 'Invalid password reset.';
}

$head = c\title('Reset Password');
