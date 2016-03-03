<?php

//
function email($to, $subject, $message, $additionalHeaders) {
    if(EMAIL_IS_SEND) {
        mail($to, $subject, $message, $additionalHeaders);
    }

    if(EMAIL_IS_LOG) {
        $data = "to: $to
subject: $subject
additional headers: $additionalHeaders
message: $message\n\n\n\n";

        file_put_contents(EMAIL_LOG_FILE, $data, FILE_APPEND);
    }
}

//
function email_states($user_data, $form_data) {
    return array(
        'is_new' => !$user_data['email'] && $form_data['email'],
        'is_deleted' => $user_data['email'] && !$form_data['email'],
        'is_changed' => $user_data['email'] && $form_data['email']
            && $user_data['email'] != $form_data['email']);
}

//
function email_taken($email) {
    return "Email \"$email\" already in use";
}

//
function invalid_password($inputName) {
    return "$inputName must be at least 8 characters";
}

//
function is_password($value) {
    return preg_match('/^.{8,100}$/', $value);
}

//
function username_taken($username) {
    return "Username \"$username\" already in use";
}

//
function validate_password($value, $inputName) {
    if(!is_password($value)) {
        return invalid_password($inputName);
    }
}

//
function validate_username($value) {
    if(!preg_match('/^[a-z0-9-]{4,16}$/i', $value)) {
        return 'Username must be 4-16 characters and use letters, numbers and dashes only';
    }
}
