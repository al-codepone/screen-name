<?php

function forgotPassword(array $formData, $errors = array()) {
    return
        '<form method="post">'
        . "<div>Submit your account email and we'll send you
            directions for resetting your password.</div>"

        . blist($errors, array('class' => 'error'))

        . input(array(
            'id' => 'email',
            'value' => $formData['email'],
            'type' => 'email'),
            'Email')

        . input(array(
            'type' => 'submit',
            'value' => 'Submit'))

        . '</form>';
}

?>
