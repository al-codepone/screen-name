<?php

function nav_items($user) {
    return $user
        ? c\li(c\hlink(ROOT, 'home')) .
          c\li(c\hlink(EDIT_ACCOUNT, 'edit account')) .
          c\li(c\hlink(LOG_OUT, 'log out')) .
          c\li(c\esc($user['username']))

        : c\li(c\hlink(ROOT, 'home')) .
          c\li(c\hlink(SIGN_UP, 'sign up')) .
          c\li(c\hlink(LOGIN, 'log in'));
}
