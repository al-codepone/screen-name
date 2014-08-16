<?php

$validator = new vanilla\forms\EditAccountValidator();

if(!$user) {
    $content = 'Log in to edit your account.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($formData['delete_flag']) {
        $content = ($error = $userModel->deleteUser($user['user_id'], $formData))
            ? editAccount($formData, $error)
            : 'Your account was successfully deleted.';

        $user = $error ? $user : null;
    }
    else if($errors) {
        $content = editAccount($formData, $errors);
    }
    else {
        $content = is_array($result = $userModel->updateUser($user['user_id'], $formData))
            ? accountUpdated($result, $formData)
            : editAccount($formData, $result);

        $user['username'] = is_array($result) ? $formData['username'] : $user['username'];
    }
}
else {
    $userData = $userModel->getUserWithUID($user['user_id']);
    $formData = $validator->values();
    $formData['username'] = $userData['username'];
    $formData['email'] = $userData['email'];
    $content = editAccount($formData);
}

$head = '<title>Edit Account</title>
    <script src="' . JS . 'edit-account.js"></script>';

?>
