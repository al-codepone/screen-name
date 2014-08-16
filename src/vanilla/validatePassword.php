<?php

function validatePassword($value, $inputName) {
    if(!isPassword($value)) {
        return invalidPassword($inputName);
    }
}

?>
