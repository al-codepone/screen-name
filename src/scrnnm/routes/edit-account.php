<?php

$validator = new scrnnm\validator\EditAccountValidator();

if(!$user) {
    $t_content = 'Log in to edit your account.';
}
else if(list($form_data, $errors) = $validator->validate()) {
    if($form_data['delete_flag']) {
        $t_content = ($error = $user_model->deleteUser($user['user_id'], $form_data))
            ? edit_account($form_data, $error)
            : 'Your account was successfully deleted.';

        $user = $error ? $user : null;
    }
    else if($errors) {
        $t_content = edit_account($form_data, $errors);
    }
    else {
        $t_content = is_array($result = $user_model->updateUser($user['user_id'], $form_data))
            ? account_updated($result, $form_data)
            : edit_account($form_data, $result);

        $user['username'] = is_array($result) ? $form_data['username'] : $user['username'];
    }
}
else {
    $userData = $user_model->getUserWithUID($user['user_id']);
    $form_data = $validator->values();
    $form_data['username'] = $userData['username'];
    $form_data['email'] = $userData['email'];
    $t_content = edit_account($form_data);
}

$t_head = c\title('Edit Account') .
    c\js(JS . 'edit-account.js');
