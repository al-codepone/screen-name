<?php

function emailStates($userData, $formData) {
    return array(
        'is_new' => !$userData['email'] && $formData['email'],
        'is_deleted' => $userData['email'] && !$formData['email'],
        'is_changed' => $userData['email'] && $formData['email']
            && $userData['email'] != $formData['email']);
}

?>
