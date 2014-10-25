<?php

use scrnnm\db\ModelFactory;

$validator = new scrnnm\validator\ForgotPasswordValidator();

if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $content = forgot_password($formData, $errors);
    }
    else {
        $resetPasswordModel = ModelFactory::get('scrnnm\db\ResetPasswordModel');
        $resetPasswordModel->createToken($formData['email']);
        $content = sprintf("If the email address you entered, %s,
            is associated with a user account, then you will receive
            an email with directions for resetting your password. If
            you don't receive this email then please check your junk mail folder.",
            $formData['email']);
    }
}
else {
    $autofocus = autofocus('email');
    $content = forgot_password($validator->values());
}

$head = c\title('Forgot Password');
