<?php

$validator = new scrnnm\validator\EditAccountValidator();

if(!$user) {
    $content = 'Log in to edit your account.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($formData['delete_flag']) {
        $content = ($error = $user_model->deleteUser($user['user_id'], $formData))
            ? edit_account($formData, $error)
            : 'Your account was successfully deleted.';

        $user = $error ? $user : null;
    }
    else if($errors) {
        $content = edit_account($formData, $errors);
    }
    else {
        $content = is_array($result = $user_model->updateUser($user['user_id'], $formData))
            ? account_updated($result, $formData)
            : edit_account($formData, $result);

        $user['username'] = is_array($result) ? $formData['username'] : $user['username'];
    }
}
else {
    $userData = $user_model->getUserWithUID($user['user_id']);
    $formData = $validator->values();
    $formData['username'] = $userData['username'];
    $formData['email'] = $userData['email'];
    $content = edit_account($formData);
}

$head = c\title('Edit Account') .
    c\js(JS . 'edit-account.js');
