<?php

function isPassword($value) {
    return preg_match('/^.{8,100}$/', $value);
}

?>
