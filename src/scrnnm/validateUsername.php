<?php

function validateUsername($value) {
    if(!preg_match('/^[a-z0-9-]{4,16}$/i', $value)) {
        return 'Username must be 4-16 characters and use letters, numbers and dashes only';
    }
}

?>
