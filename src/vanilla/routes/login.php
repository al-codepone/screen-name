<?php

$validator = new vanilla\forms\LoginValidator();

if($user) {
    $content = 'You are already logged in.';
}
else if(list($formData, $errors) = $validator->validate()) {
    if($errors) {
        $content = login($formData, $errors);
    }
    else if($error = $loginModel->login($formData)) {
        $content = login($formData, $error);
    }
    else {
        header('Location:' . ROOT);
        exit();
    }
}
else {
    $autofocus = autofocus('username');
    $content = login($validator->values());
}

$head = '<title>Log In</title>';

?>
