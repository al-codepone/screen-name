<?php

namespace scrnnm\model;

use pjsql\DatabaseAdapter;
use pjsql\DatabaseHandle;

class UserModel extends DatabaseAdapter {
    protected $persistent_model;

    public function __construct(DatabaseHandle $database_handle) {
        parent::__construct($database_handle);
        $this->persistent_model = ModelFactory::get('scrnnm\model\PersistentLoginModel');
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

        $this->persistent_model->install();
    }

    //
    public function createUser($data) {
        if($this->getUserWithUsername($data['username'])) {
            return username_taken($data['username']);
        }
        else if($this->getUserWithEmail($data['email'])) {
            return email_taken($data['email']);
        }
        else {
            $this->exec(sprintf('
                INSERT INTO tuser
                    (username, password)
                VALUES
                    ("%s", "%s")',
                $this->esc($data['username']),
                $this->esc(\pc\bcrypt_hash($data['password'], BCRYPT_COST))));

            if($data['email']) {
                $user_id = $this->conn()->insert_id;
                $verify_model = ModelFactory::get('scrnnm\model\VerifyEmailModel');
                $verify_model->create($user_id, $data['username'], $data['email']);
            }
        }
    }

    //
    public function getUserWithUID($user_id) {
        return $this->getUser(sprintf('user_id = %d', $user_id));
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
        else if(IS_REMEMBER_ME && $data = $this->persistent_model->get()) {
            $this->persistent_model->delete($data['token_id']);
            $this->persistent_model->create($data['user_id']);

            $_SESSION[SESSION_USER_ID] = $data['user_id'];
            $_SESSION[SESSION_USERNAME] = $data['username'];
            return $this->getSessionUser();
        }
    }

    //this is called when the user submits the edit account form
    public function updateUser($user_id, $form_data) {
        $user_data = $this->getUserWithUID($user_id);
        $username_user_data = $this->getUserWithUsername($form_data['username']);
        $email_user_data = $this->getUserWithEmail($form_data['email']);
        $email_states = email_states($user_data, $form_data);

        if($user_data['password'] != \pc\bcrypt_hash($form_data['current_password'], $user_data['password'])) {
            return 'Incorrect current password';
        }
        else if($username_user_data && $user_id != $username_user_data['user_id']) {
            return username_taken($form_data['username']);
        }
        else if($email_user_data && $user_id != $email_user_data['user_id']) {
            return email_taken($form_data['email']);
        }

        if($email_states['is_new'] || $email_states['is_changed']) {
            $verify_model = ModelFactory::get('scrnnm\model\VerifyEmailModel');
            $verify_model->create($user_id, $form_data['username'], $form_data['email']);
        }

        if($email_states['is_deleted'] || $email_states['is_changed']) {
            $this->updateEmail($user_id, '');
        }

        $setPassword = $form_data['password']
            ? sprintf(', password = "%s"', $this->esc(\pc\bcrypt_hash($form_data['password'], BCRYPT_COST)))
            : '';

        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                username = "%s"%s
            WHERE
                user_id = %d',
            $this->esc($form_data['username']),
            $setPassword,
            $user_id));

        $_SESSION[SESSION_USERNAME] = $form_data['username'];
        return $user_data;
    }

    //
    public function updateEmail($user_id, $email) {
        if($this->getUserWithEmail($email)) {
            return email_taken($email);
        }

        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                email = "%s"
            WHERE
                user_id = %d',
            $this->esc($email),
            $user_id));
    }

    //
    public function updatePassword($user_id, $data) {
        $this->exec(sprintf('
            UPDATE
                tuser
            SET
                password = "%s"
            WHERE
                user_id = %d',
            $this->esc(\pc\bcrypt_hash($data['password'], BCRYPT_COST)),
            $user_id));
    }

    //
    public function deleteUser($user_id, $form_data) {
        $user_data = $this->getUserWithUID($user_id);

        if($user_data['password'] != \pc\bcrypt_hash($form_data['current_password'], $user_data['password'])) {
            return 'Incorrect current password';
        }

        $this->exec(sprintf('
            DELETE FROM
                tuser
            WHERE
                user_id = %d',
            $user_id));

        unset($_SESSION[SESSION_USER_ID]);
    }

    //
    public function prune() {
        $this->persistent_model->prune();
    }

    //
    public function login(array $form_data) {
        $user_data = $this->getUserWithUsername($form_data['username']);

        if(!$user_data || $user_data['password'] != \pc\bcrypt_hash($form_data['password'], $user_data['password'])) {
            return 'Incorrect username and password';
        }

        if(IS_REMEMBER_ME && $form_data['remember_me']) {
            $this->persistent_model->create($user_data['user_id']);
        }

        $_SESSION[SESSION_USER_ID] = $user_data['user_id'];
        $_SESSION[SESSION_USERNAME] = $user_data['username'];
    }

    //
    public function logOut() {
        if($data = $this->persistent_model->get()) {
            $this->persistent_model->delete($data['token_id']);
        }

        setcookie(COOKIE_PERSISTENT_LOGIN, '', time() - 3600);
        unset($_SESSION[SESSION_USER_ID]);
    }

    //
    protected function getSessionUser() {
        return array(
            'user_id' => $_SESSION[SESSION_USER_ID],
            'username' => $_SESSION[SESSION_USERNAME]);
    }

    //
    protected function getUser($condition) {
        $data = $this->query(sprintf('
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

        return $data[0];
    }
}
