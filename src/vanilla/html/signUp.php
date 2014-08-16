<?php

function signUp(array $formData, $errors = array()) {
    return
        '<form method="post">'
        . blist($errors, array('class' => 'error'))
        . input(array(
            'id' => 'username',
            'value' => $formData['username']),
            'Username')

        . input(array(
            'id' => 'email',
            'value' => $formData['email'],
            'type' => 'email'),
            'Email(optional)')

        . input(array(
            'id' => 'password',
            'type' => 'password'),
            'Password')

        . input(array(
            'id' => 'confirm_password',
            'type' => 'password'),
            'Confirm Password')

        . input(array(
            'type' => 'submit',
            'value' => 'Sign Up'))

        . '</form>';
}

?>
