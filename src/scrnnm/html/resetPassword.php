<?php

function resetPassword(array $form_data, $errors = array()) {
    return c\form(
        array('method' => 'post'),
        c\div('Use this form to reset your password.'),
        c\ulist($errors, array('class' => 'error')),
        c\dlinput(
            'New Password',
            array(
                'id' => 'password',
                'type' => 'password')),

        c\dlinput(
            'Confirm New Password',
            array(
                'id' => 'confirm_password',
                'type' => 'password')),

        c\div('<input type="submit" value="Reset"/>'));
}
