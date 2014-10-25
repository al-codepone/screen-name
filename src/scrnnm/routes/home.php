<?php

$head = c\title('Home');
$content = sprintf('Welcome %s.', $user
    ? $user['username']
    : 'guest');
