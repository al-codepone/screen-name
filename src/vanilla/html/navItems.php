<?php

function navItems($user) {
    return $user 
        ? sprintf('<li><a href="%s">home</a></li>
            <li><a href="%s">edit account</a></li>
            <li><a href="%s">log out</a></li>
            <li>%s</li>',
            ROOT, EDIT_ACCOUNT, LOG_OUT, $user['username'])

        : sprintf('<li><a href="%s">home</a></li>
            <li><a href="%s">sign up</a></li>
            <li><a href="%s">log in</a></li>',
            ROOT, SIGN_UP, LOGIN);
}

?>
