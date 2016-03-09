<?php

use scrnnm\model\ModelFactory;

$validator = new scrnnm\validator\ForgotPasswordValidator();

if(list($form_data, $errors) = $validator->validate()) {
    if($errors) {
        $t_content = forgot_password($form_data, $errors);
    }
    else {
        $reset_model = ModelFactory::get('scrnnm\model\ResetPassword');
        $reset_model->create($form_data['email']);
        $t_content = sprintf("If the email address you entered, %s,
            is associated with a user account, then you will receive
            an email with directions for resetting your password. If
            you don't receive this email then please check your junk mail folder.",
            $form_data['email']);
    }
}
else {
    $t_last = c\focus('email');
    $t_content = forgot_password($validator->values());
}

$t_head = c\title('Forgot Password');
