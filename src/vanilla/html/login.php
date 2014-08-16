<?php

function login(array $formData, $errors = array()) {
    return
        '<form method="post">'
        . blist($errors, array('class' => 'error'))
        . input(array(
            'id' => 'username',
            'value' => $formData['username']),
            'Username')

        . input(array(
            'id' => 'password',
            'type' => 'password'),
            'Password')

        . input(array(
            'id' => 'remember_me',
            'value' => 1,
            'type' => 'checkbox',
            $formData['remember_me'] ? 'checked' : ''),
            'Remember Me')

        . sprintf('<div><a href="%s">forgot password</a></div>',
            FORGOT_PASSWORD)

        . input(array(
            'type' => 'submit',
            'value' => 'Log In'))

        . '</form>';
}

?>
