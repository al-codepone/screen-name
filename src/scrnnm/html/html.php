<?php

//
function account_updated($user_data, $form_data) {
    $state = emailStates($user_data, $form_data);
    $sentence = '';

    if($state['is_changed']) {
        $sentence = 'We emailed you a link to verify your updated email.
            Your old email was removed from your account.';
    }
    else if($state['is_new']) {
        $sentence = 'We emailed you a link to verify your email.';
    }
    else if($state['is_deleted']) {
        $sentence = 'Your email was removed from your account.';
    }

    return "Your account has been updated. $sentence";
}

//
function edit_account(array $form_data, $errors = array()) {
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

//
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

//
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

//
function nav_items($user) {
    return $user
        ? c\li(c\hlink(ROOT, 'home')) .
          c\li(c\hlink(EDIT_ACCOUNT, 'edit account')) .
          c\li(c\hlink(LOG_OUT, 'log out')) .
          c\li(c\esc($user['username']))

        : c\li(c\hlink(ROOT, 'home')) .
          c\li(c\hlink(SIGN_UP, 'sign up')) .
          c\li(c\hlink(LOGIN, 'log in'));
}

//
function reset_password(array $form_data, $errors = array()) {
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

//
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

        c\div('<input type="submit" value="Sign Up"/>'));
}
