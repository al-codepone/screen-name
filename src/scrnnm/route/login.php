<?php

$validator = new scrnnm\validator\LoginValidator();

if($user) {
    $t_content = 'You are already logged in.';
}
else if(list($form_data, $errors) = $validator->validate()) {
    if($errors) {
        $t_content = login($form_data, $errors);
    }
    else if($error = $user_model->login($form_data)) {
        $t_content = login($form_data, $error);
    }
    else {
        header('Location:' . ROOT);
        exit();
    }
}
else {
    $t_last = c\focus('username');
    $t_content = login($validator->values());
}

$t_head = c\title('Log In');
