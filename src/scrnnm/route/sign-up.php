<?php

$validator = new scrnnm\validator\SignUpValidator();

if($user) {
    $t_content = 'You are already signed up.';
}
else if(list($form_data, $errors) = $validator->validate()) {
    if($errors) {
        $t_content = sign_up($form_data, $errors);
    }
    else if($error = $user_model->create($form_data)) {
        $t_content = sign_up($form_data, $error);
    }
    else {
        $t_content = sprintf('Thank you for signing up.
            You can now <a href="%s">log in</a>. %s',
            LOGIN, $form_data['email']
                ? 'We emailed you a link to verify your email.'
                : 'You can assign an email to your account on the edit account page.');
    }
}
else {
    $t_last = c\focus('username');
    $t_content = sign_up($validator->values());
}

$t_head = c\title('Sign Up');
