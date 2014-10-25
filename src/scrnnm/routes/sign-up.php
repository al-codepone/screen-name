<?php

$validator = new scrnnm\validator\SignUpValidator();

if($user) {
    $content = 'You are already signed up.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $content = sign_up($formData, $errors);
    }
    else if($error = $userModel->createUser($formData)) {
        $content = sign_up($formData, $error);
    }
    else {
        $content = sprintf('Thank you for signing up.
            You can now <a href="%s">log in</a>. %s',
            LOGIN, $formData['email']
                ? 'We emailed you a link to verify your email.'
                : 'You can assign an email to your account on the edit account page.');
    }
}
else {
    $autofocus = autofocus('username');
    $content = sign_up($validator->values());
}

$head = c\title('Sign Up');
