<?php

function forgot_password(array $form_data, $errors = array()) {
    return c\form(
        array('method' => 'post'),
        c\div("Submit your account email and we'll send you
            directions for resetting your password."),

        c\ulist($errors, array('class' => 'error')),
        c\dlinput(
            'Email',
            array(
                'id' => 'email',
                'value' => $form_data['email'],
                'type' => 'email')),

        c\div('<input type="submit" value="Submit"/>'));
}
