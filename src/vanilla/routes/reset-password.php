<?php

use vanilla\db\ModelFactory;

$resetPasswordModel = ModelFactory::get('vanilla\db\ResetPasswordModel');
$tokenData = $resetPasswordModel->getToken($_GET['id'], $_GET['token']);

if($tokenData) {
    $validator = new vanilla\forms\ResetPasswordValidator();

    if(list($formData, $errors) = $validator->validate()) {
        if($errors) {
            $content = resetPassword($formData, $errors);
        }
        else if($error = $userModel->updatePassword($tokenData['user_id'], $formData)) {
            $content = resetPassword($formData, $error);
        }
        else {
            $resetPasswordModel->deleteToken($tokenData['token_id']);
            $content = 'Your password was successfully reset.';
        }
    }
    else {
        $autofocus = autofocus('password');
        $content = resetPassword($validator->values());
    }
}
else {
    $content = 'Invalid password reset.';
}

$head = '<title>Reset Password</title>';

?>
