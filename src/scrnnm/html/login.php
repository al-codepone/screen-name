<?php

function login(array $form_data, $errors = array()) {
    return c\form(
        array('method' => 'post'),
        c\ulist($errors, array('class' => 'error')),
        c\dlinput(
            'Username',
            array(
                'id' => 'username',
                'value' => $form_data['username'])),

        c\dlinput(
            'Password',
            array(
                'id' => 'password',
                'type' => 'password')),

        c\dlinput(
            'Remember Me',
            array(
                'id' => 'remember_me',
                'value' => 1,
                'type' => 'checkbox',
                $form_data['remember_me'] ? 'checked' : '')),

        c\div(c\hlink(FORGOT_PASSWORD, 'forgot password')),
        c\div('<input type="submit" value="Log In"/>'));
}
