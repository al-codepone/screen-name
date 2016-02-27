<?php

$t_head = c\title('Home');
$t_content = sprintf('Welcome %s.', $user
    ? $user['username']
    : 'guest');
