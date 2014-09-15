<?php

$validator = new scrnnm\validator\EditAccountValidator();

if(!$user) {
    $content = 'Log in to edit your account.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($formData['delete_flag']) {
        $content = ($error = $userModel->deleteUser($user['user_id'], $formData))
            ? edit_account($formData, $error)
            : 'Your account was successfully deleted.';

        $user = $error ? $user : null;
    }
    else if($errors) {
        $content = edit_account($formData, $errors);
    }
    else {
        $content = is_array($result = $userModel->updateUser($user['user_id'], $formData))
            ? accountUpdated($result, $formData)
            : edit_account($formData, $result);

        $user['username'] = is_array($result) ? $formData['username'] : $user['username'];
    }
}
else {
    $userData = $userModel->getUserWithUID($user['user_id']);
    $formData = $validator->values();
    $formData['username'] = $userData['username'];
    $formData['email'] = $userData['email'];
    $content = edit_account($formData);
}

$head = '<title>Edit Account</title>
    <script src="' . JS . 'edit-account.js"></script>';
