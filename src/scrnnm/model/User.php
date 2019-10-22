<?php

namespace scrnnm\model;

use pjsql\DatabaseAdapter;
use pjsql\DatabaseHandle;

class User extends DatabaseAdapter {
    protected $persistent_model;

    public function __construct(DatabaseHandle $database_handle) {
        parent::__construct($database_handle);
        $this->persistent_model = ModelFactory::get('scrnnm\model\PersistentLogin');
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
    public function create(array $form_data) {
        if($this->getWithUsername($form_data['username'])) {
            return username_taken($form_data['username']);
        }
        else if($this->getWithEmail($form_data['email'])) {
            return email_taken($form_data['email']);
        }
        else {
            $this->exec('
                INSERT INTO tuser
                    (username, password)
                VALUES
                    (?, ?)',
                $form_data['username'],
                \pc\bcrypt_hash($form_data['password'], BCRYPT_COST));

            if($form_data['email']) {
                $user_id = $this->conn()->insert_id;
                $verify_model = ModelFactory::get('scrnnm\model\VerifyEmail');
                $verify_model->create($user_id, $form_data['username'], $form_data['email']);
            }
        }
    }

    //
    public function getWithId($user_id) {
        return $this->get($user_id);
    }

    //
    public function getWithUsername($username) {
        return $this->get(-1, $username);
    }

    //
    public function getWithEmail($email) {
        return $email
            ? $this->get(-1, '', $email)
            : null;
    }

    //
    public function getActive() {
        if(isset($_SESSION[SESSION_USER_ID])) {
            return $this->getSession();
        }
        else if(IS_REMEMBER_ME && $data = $this->persistent_model->get()) {
            $this->persistent_model->delete($data['token_id']);
            $this->persistent_model->create($data['user_id']);

            $_SESSION[SESSION_USER_ID] = $data['user_id'];
            $_SESSION[SESSION_USERNAME] = $data['username'];
            return $this->getSession();
        }
    }

    //this is called when the user submits the edit account form
    public function update($user_id, array $form_data) {
        $user_data = $this->getWithId($user_id);
        $username_user_data = $this->getWithUsername($form_data['username']);
        $email_user_data = $this->getWithEmail($form_data['email']);
        $email_states = email_states($user_data, $form_data);
        $compute_hash = \pc\bcrypt_hash(
            $form_data['current_password'],
            $user_data['password']);

        if($user_data['password'] != $compute_hash) {
            return 'Incorrect current password';
        }
        else if($username_user_data && $user_id != $username_user_data['user_id']) {
            return username_taken($form_data['username']);
        }
        else if($email_user_data && $user_id != $email_user_data['user_id']) {
            return email_taken($form_data['email']);
        }

        if($email_states['is_new'] || $email_states['is_changed']) {
            $verify_model = ModelFactory::get('scrnnm\model\VerifyEmail');
            $verify_model->create(
                $user_id,
                $form_data['username'],
                $form_data['email']);
        }

        if($email_states['is_deleted'] || $email_states['is_changed']) {
            $this->updateEmail($user_id, '');
        }

        $this->exec('
            UPDATE
                tuser
            SET
                username = ?,
                password = if(? <> "", ?, password)
            WHERE
                user_id = ?',
            $form_data['username'],
            $form_data['password'],
            \pc\bcrypt_hash($form_data['password'], BCRYPT_COST),
            $user_id);

        $_SESSION[SESSION_USERNAME] = $form_data['username'];
        return $user_data;
    }

    //
    public function updateEmail($user_id, $email) {
        if($this->getWithEmail($email)) {
            return email_taken($email);
        }

        $this->exec('
            UPDATE
                tuser
            SET
                email = ?
            WHERE
                user_id = ?',
            $email,
            $user_id);
    }

    //
    public function updatePassword($user_id, array $form_data) {
        $this->exec('
            UPDATE
                tuser
            SET
                password = ?
            WHERE
                user_id = ?',
            \pc\bcrypt_hash($form_data['password'], BCRYPT_COST),
            $user_id);
    }

    //
    public function delete($user_id, array $form_data) {
        $user_data = $this->getWithId($user_id);
        $compute_hash = \pc\bcrypt_hash(
            $form_data['current_password'],
            $user_data['password']);

        if($user_data['password'] != $compute_hash) {
            return 'Incorrect current password';
        }

        $this->exec('
            DELETE FROM
                tuser
            WHERE
                user_id = ?',
            $user_id);

        unset($_SESSION[SESSION_USER_ID]);
    }

    //
    public function prune() {
        $this->persistent_model->prune();
    }

    //
    public function login(array $form_data) {
        $user_data = $this->getWithUsername($form_data['username']);
        $compute_hash = \pc\bcrypt_hash(
            $form_data['password'],
            $user_data['password']);

        if(!$user_data || $user_data['password'] != $compute_hash) {
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
    protected function getSession() {
        return array(
            'user_id' => $_SESSION[SESSION_USER_ID],
            'username' => $_SESSION[SESSION_USERNAME]);
    }

    //
    protected function get($user_id, $username = '', $email = 'x') {
        $data = $this->query('
            SELECT
                user_id,
                username,
                email,
                password
            FROM
                tuser
            WHERE
                user_id = ? or
                username = ? or
                email = ?',
            $user_id,
            $username,
            $email);

        return $data[0];
    }
}
