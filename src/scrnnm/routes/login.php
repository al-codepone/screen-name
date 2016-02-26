<?php

$validator = new scrnnm\validator\LoginValidator();

if($user) {
    $content = 'You are already logged in.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $content = login($formData, $errors);
    }
    else if($error = $user_model->login($formData)) {
        $content = login($formData, $error);
    }
    else {
        header('Location:' . ROOT);
        exit();
    }
}
else {
    $autofocus = c\focus('username');
    $content = login($validator->values());
}

$head = c\title('Log In');
