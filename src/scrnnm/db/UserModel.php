<?php

namespace scrnnm\db;

use pjsql\DatabaseAdapter;
use pjsql\DatabaseHandle;

class UserModel extends DatabaseAdapter {
    protected $login_token;

    public function __construct(DatabaseHandle $database_handle) {
        parent::__construct($database_handle);

        //
        $this->login_token = new TokenModel(
            $database_handle,
            TABLE_PERSISTENT_LOGIN_TOKENS,
            TTL_PERSISTENT_LOGIN);
    }

    //
    public function install() {
        $this->exec('
            CREATE TABLE tuser (
                user_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(32) UNIQUE,
                email VARCHAR(255) DEFAULT "",
                password VARCHAR(128))
            ENGINE = MYISAM');

        $this->login_token->install();
    }

    //
    public function createUser($data) {
        if($this->getUserWithUsername($data['username'])) {
            return usernameTaken($data['username']);
        }
        else if($this->getUserWithEmail($data['email'])) {
            return emailTaken($data['email']);
        }
        else {
            $this->exec(sprintf('
                INSERT INTO tuser
                    (username, password)
                VALUES
                    ("%s", "%s")',
                $this->esc($data['username']),
                $this->esc(\pc\bcrypt_hash($data['password'], BCRYPT_COST))));

            $userID = $this->conn()->insert_id;

            if($data['email']) {
                $verifyEmailModel = ModelFactory::get('scrnnm\db\VerifyEmailModel');
                $verifyEmailModel->createToken($userID, $data['username'], $data['email']);
            }
        }
    }

    //
    public function getUserWithUID($userID) {
        return $this->getUser(sprintf('user_id = %d', $userID));
    }

    //
    public function getUserWithUsername($username) {
        return $this->getUser(sprintf('username = "%s"',
            $this->esc($username)));
    }

    //
    public function getUserWithEmail($email) {
        return $email
            ? $this->getUser(sprintf('email = "%s"', $this->esc($email)))
            : null;
    }

    //
    public function getActiveUser() {
        if(isset($_SESSION[SESSION_USER_ID])) {
            return $this->getSessionUser();
        }
        else if($data = $this->getPersistentLogin()) {
            $this->login_token->deleteToken($data['token_id']);
            $this->createPersistentLogin($data['user_id']);

            $_SESSION[SESSION_USER_ID] = $data['user_id'];
            $_SESSION[SESSION_USERNAME] = $data['username'];
            return $this->getSessionUser();
        }
    }

    //this is called when the user submits the edit account form
    public function updateUser($userID, $formData) {
        $userData = $this->getUserWithUID($userID);
        $usernameUserData = $this->getUserWithUsername($formData['username']);
        $emailUserData = $this->getUserWithEmail($formData['email']);
        $emailStates = emailStates($userData, $formData);

        if($userData['password'] != \pc\bcrypt_hash($formData['current_password'], $userData['password'])) {
            return 'Incorrect current password';
        }
        else if($usernameUserData && $userID != $usernameUserData['user_id']) {
            return usernameTaken($formData['username']);
        }
        else if($emailUserData && $userID != $emailUserData['user_id']) {
            return emailTaken($formData['email']);
        }

        if($emailStates['is_new'] || $emailStates['is_changed']) {
            $verifyEmailModel = ModelFactory::get('scrnnm\db\VerifyEmailModel');
            $verifyEmailModel->createToken($userID, $formData['username'], $formData['email']);
        }

        if($emailStates['is_deleted'] || $emailStates['is_changed']) {
            $this->updateEmail($userID, '');
        }

        $setPassword = $formData['password']
            ? sprintf(', password = "%s"', $this->esc(\pc\bcrypt_hash($formData['password'], BCRYPT_COST)))
            : '';

        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                username = "%s"%s
            WHERE
                user_id = %d',
            $this->esc($formData['username']),
            $setPassword,
            $userID));

        $_SESSION[SESSION_USERNAME] = $formData['username'];
        return $userData;
    }

    //
    public function updateEmail($userID, $email) {
        if($this->getUserWithEmail($email)) {
            return emailTaken($email);
        }

        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                email = "%s"
            WHERE
                user_id = %d',
            $this->esc($email),
            $userID));
    }

    //
    public function updatePassword($userID, $data) {
        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                password = "%s"
            WHERE
                user_id = %d',
            $this->esc(\pc\bcrypt_hash($data['password'], BCRYPT_COST)),
            $userID));
    }

    //
    public function deleteUser($userID, $formData) {
        $userData = $this->getUserWithUID($userID);

        if($userData['password'] != \pc\bcrypt_hash($formData['current_password'], $userData['password'])) {
            return 'Incorrect current password';
        }

        $this->exec(sprintf('
            DELETE FROM
                tuser
            WHERE
                user_id = %d',
            $userID));

        unset($_SESSION[SESSION_USER_ID]);
    }

    //
    public function prune() {
        $this->login_token->prune();
    }

    //
    public function login(array $form_data) {
        $user_data = $this->getUserWithUsername($form_data['username']);

        if(!$user_data || $user_data['password'] != \pc\bcrypt_hash($form_data['password'], $user_data['password'])) {
            return 'Incorrect username and password';
        }

        if($form_data['remember_me']) {
            $this->createPersistentLogin($user_data['user_id']);
        }

        $_SESSION[SESSION_USER_ID] = $user_data['user_id'];
        $_SESSION[SESSION_USERNAME] = $user_data['username'];
    }

    //
    public function logOut() {
        if($data = $this->getPersistentLogin()) {
            $this->login_token->deleteToken($data['token_id']);
        }

        setcookie(COOKIE_PERSISTENT_LOGIN, '', time() - 3600);
        unset($_SESSION[SESSION_USER_ID]);
    }

    //
    protected function createPersistentLogin($userID) {
        $token = \pc\sha1_token();
        $this->login_token->createToken($userID, $token);
        setcookie(COOKIE_PERSISTENT_LOGIN, "$userID.$token",
            time() + 60*60*24*TTL_PERSISTENT_LOGIN);
    }

    //
    protected function getPersistentLogin() {
        if($_COOKIE[COOKIE_PERSISTENT_LOGIN]) {
            list($userID, $token) = explode('.', $_COOKIE[COOKIE_PERSISTENT_LOGIN]);
            return $this->login_token->getToken($userID, $token);
        }
    }

    //
    protected function getSessionUser() {
        return array(
            'user_id' => $_SESSION[SESSION_USER_ID],
            'username' => $_SESSION[SESSION_USERNAME]);
    }

    //
    protected function getUser($condition) {
        $queryData = $this->query(sprintf('
            SELECT
                user_id,
                username,
                email,
                password
            FROM
                tuser
            WHERE
                %s',
            $condition));

        return $queryData[0];
    }
}
