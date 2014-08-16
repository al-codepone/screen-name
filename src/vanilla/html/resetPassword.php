<?php

function resetPassword(array $formData, $errors = array()) {
    return
        '<form method="post">
        <div>Use this form to reset your password.</div>'
        . blist($errors, array('class' => 'error'))
        . input(array(
            'id' => 'password',
            'type' => 'password'),
            'New Password')

        . input(array(
            'id' => 'confirm_password',
            'type' => 'password'),
            'Confirm New Password')

        . input(array(
            'type' => 'submit',
            'value' => 'Reset'))

        . '</form>';
}

?>
