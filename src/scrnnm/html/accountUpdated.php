<?php

function accountUpdated($userData, $formData) {
    $state = emailStates($userData, $formData);
    $sentence = '';

    if($state['is_changed']) {
        $sentence = ' We emailed you a link to verify your updated email.
            Your old email was removed from your account.';
    }
    else if($state['is_new']) {
        $sentence = ' We emailed you a link to verify your email.';
    }
    else if($state['is_deleted']) {
        $sentence = ' Your email was removed from your account.';
    }

    return "Your account has been updated.$sentence";
}

?>
