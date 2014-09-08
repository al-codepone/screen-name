<?php

function editAccount(array $form_data, $errors = array()) {
    return c\form(
        array(
            'method' => 'post',
            'id' => 'edit_account_form'),

        '<input type="hidden" name="delete_flag" value="0"/>',
        c\div('Use this form to edit your account info.
            Email and new password are optional.
            You can delete your account email by submitting a blank email.
            If you change your email, then your account email will be blank
            until you verify the new email.'),

        c\ulist($errors, array('class' => 'error')),
        c\dlinput(
            'Username',
            array(
                'id' => 'username',
                'value' => $form_data['username'])),

        c\dlinput(
            'Email',
            array(
                'id' => 'email',
                'value' => $form_data['email'],
                'type' => 'email')),

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

        c\dlinput(
            'Current Password',
            array(
                'id' => 'current_password',
                'type' => 'password')),

        c\div(
            '<input type="submit" value="Submit"/>',
            '<input type="button" value="Delete Account" onclick="deleteAccount();"/>'));
}
