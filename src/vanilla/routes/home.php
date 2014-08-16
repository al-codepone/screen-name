<?php

$head = '<title>Home</title>';
$content = sprintf('Welcome %s.', $user
    ? $user['username']
    : 'guest');

?>
