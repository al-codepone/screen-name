<?php

function sign_up(array $form_data, $errors = array()) {
    return c\form(
        array('method' => 'post'),
        c\ulist($errors, array('class' => 'error')),
        c\dlinput(
            'Username',
            array(
                'id' => 'username',
                'value' => $form_data['username'])),

        c\dlinput(
            'Email(optional)',
            array(
                'id' => 'email',
                'value' => $form_data['email'],
                'type' => 'email')),

        c\dlinput(
            'Password',
            array(
                'id' => 'password',
                'type' => 'password')),

        c\dlinput(
            'Confirm Password',
            array(
                'id' => 'confirm_password',
                'type' => 'password')),

        c\div('<input type="submit" value="Sign Up"'));
}
