<?php

use scrnnm\db\ModelFactory;

$resetPasswordModel = ModelFactory::get('scrnnm\db\ResetPasswordModel');
$tokenData = $resetPasswordModel->getToken($_GET['id'], $_GET['token']);

if($tokenData) {
    $validator = new scrnnm\validator\ResetPasswordValidator();

    if(list($form_data, $errors) = $validator->validate()) {
        if($errors) {
            $t_content = reset_password($form_data, $errors);
        }
        else if($error = $user_model->updatePassword($tokenData['user_id'], $form_data)) {
            $t_content = reset_password($form_data, $error);
        }
        else {
            $resetPasswordModel->deleteToken($tokenData['token_id']);
            $t_content = 'Your password was successfully reset.';
        }
    }
    else {
        $t_last = c\focus('password');
        $t_content = reset_password($validator->values());
    }
}
else {
    $t_content = 'Invalid password reset.';
}

$t_head = c\title('Reset Password');
