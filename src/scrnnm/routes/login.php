<?php

$validator = new scrnnm\validator\LoginValidator();

if($user) {
    $t_content = 'You are already logged in.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $t_content = login($formData, $errors);
    }
    else if($error = $user_model->login($formData)) {
        $t_content = login($formData, $error);
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
