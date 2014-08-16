<?php

namespace vanilla\db;

use cityphp\db\DatabaseAdapter;
use vanilla\db\ModelFactory;

class UserModel extends DatabaseAdapter {
    public function install() {
        $this->exec('CREATE TABLE ' . TABLE_USERS . ' (
            user_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(32) UNIQUE,
            email VARCHAR(255) DEFAULT "",
            password VARCHAR(128),
            PRIMARY KEY(user_id))
            ENGINE = MYISAM');
    }

    public function createUser($data) {
        if($this->getUserWithUsername($data['username'])) {
            return usernameTaken($data['username']);
        }
        else if($this->getUserWithEmail($data['email'])) {
            return emailTaken($data['email']);
        }
        else {
            $this->exec(sprintf('INSERT INTO %s (username, password) VALUES("%s", "%s")',
                TABLE_USERS,
                $this->esc($data['username']),
                $this->esc(bcryptHash($data['password'], BCRYPT_COST))));

            $userID = $this->conn()->insert_id;

            if($data['email']) {
                $verifyEmailModel = ModelFactory::get('vanilla\db\VerifyEmailModel');
                $verifyEmailModel->createToken($userID, $data['username'], $data['email']);
            }
        }
    }

    public function getUserWithUID($userID) {
        return $this->getUser(sprintf('user_id = %d', $userID));
    }

    public function getUserWithUsername($username) {
        return $this->getUser(sprintf('username = "%s"',
            $this->esc($username)));
    }

    public function getUserWithEmail($email) {
        return $email
            ? $this->getUser(sprintf('email = "%s"', $this->esc($email)))
            : null;
    }

    public function updateUser($userID, $formData) {
        $userData = $this->getUserWithUID($userID);
        $usernameUserData = $this->getUserWithUsername($formData['username']);
        $emailUserData = $this->getUserWithEmail($formData['email']);
        $emailStates = emailStates($userData, $formData);

        if($userData['password'] != bcryptHash($formData['current_password'], $userData['password'])) {
            return 'Incorrect current password';
        }
        else if($usernameUserData && $userID != $usernameUserData['user_id']) {
            return usernameTaken($formData['username']);
        }
        else if($emailUserData && $userID != $emailUserData['user_id']) {
            return emailTaken($formData['email']);
        }

        if($emailStates['is_new'] || $emailStates['is_changed']) {
            $verifyEmailModel = ModelFactory::get('vanilla\db\VerifyEmailModel');
            $verifyEmailModel->createToken($userID, $formData['username'], $formData['email']);
        }

        if($emailStates['is_deleted'] || $emailStates['is_changed']) {
            $this->updateEmail($userID, '');
        }

        $setPassword = $formData['password']
            ? sprintf(', password = "%s"', $this->esc(bcryptHash($formData['password'], BCRYPT_COST)))
            : '';

        $this->exec(sprintf('UPDATE %s SET username = "%s"%s WHERE user_id = %d',
            TABLE_USERS,
            $this->esc($formData['username']),
            $setPassword,
            $userID));

        $_SESSION[SESSION_USERNAME] = $formData['username'];
        return $userData;
    }

    public function updateEmail($userID, $email) {
        if($this->getUserWithEmail($email)) {
            return emailTaken($email);
        }

        $this->exec(sprintf('UPDATE %s SET email = "%s" WHERE user_id = %d',
            TABLE_USERS,
            $this->esc($email),
            $userID));
    }

    public function updatePassword($userID, $data) {
        $this->exec(sprintf('UPDATE %s SET password = "%s" WHERE user_id = %d',
            TABLE_USERS,
            $this->esc(bcryptHash($data['password'], BCRYPT_COST)),
            $userID));
    }

    public function deleteUser($userID, $formData) {
        $userData = $this->getUserWithUID($userID);

        if($userData['password'] != bcryptHash($formData['current_password'], $userData['password'])) {
            return 'Incorrect current password';
        }

        $this->exec(sprintf('DELETE FROM %s WHERE user_id = %d',
            TABLE_USERS,
            $userID));

        unset($_SESSION[SESSION_USER_ID]);
    }

    protected function getUser($condition) {
        $queryData = $this->query(sprintf('SELECT user_id, username, email, password
            FROM %s WHERE %s',
            TABLE_USERS,
            $condition));

        return $queryData[0];
    }
}

?>
