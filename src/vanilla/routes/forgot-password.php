<?php

use vanilla\db\ModelFactory;

$validator = new vanilla\forms\ForgotPasswordValidator();

if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $content = forgotPassword($formData, $errors);
    }
    else {
        $resetPasswordModel = ModelFactory::get('vanilla\db\ResetPasswordModel');
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
    $content = forgotPassword($validator->values());
}

$head = '<title>Forgot Password</title>';

?>
