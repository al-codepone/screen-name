<?php

namespace scrnnm\model;

use pjsql\DatabaseAdapter;
use pjsql\DatabaseHandle;

class Token extends DatabaseAdapter {
    private $table_name;
    private $ttl;

    public function __construct(DatabaseHandle $database_handle, $table_name, $ttl) {
        parent::__construct($database_handle);
        $this->table_name = $table_name;
        $this->ttl = $ttl;
    }

    public function install() {
        $this->exec('
            CREATE TABLE ' . $this->table_name . ' (
                token_id
                    INT
                    UNSIGNED
                    NOT NULL
                    AUTO_INCREMENT
                    PRIMARY KEY,
                user_id MEDIUMINT UNSIGNED,
                token VARCHAR(128),
                data VARCHAR(255),
                creation_date DATETIME)
            ENGINE = MYISAM');
    }

    public function create($user_id, $token, $data = '') {
        $this->exec('
            INSERT INTO ' . $this->table_name . '
                (user_id, token, data, creation_date)
            VALUES
                (?, ?, ?, ?)',
            $user_id,
            \pc\bcrypt_hash($token, BCRYPT_COST),
            $data,
            \pc\datetime_now());
    }

    public function get($user_id, $token) {
        $data = $this->query('
            SELECT
                t.token_id,
                t.token,
                t.data,
                u.user_id,
                u.username
            FROM
                ' . $this->table_name . ' t
            JOIN
                tuser u
            ON
                t.user_id = u.user_id
            WHERE
                t.user_id = ? AND
                t.creation_date > ? - INTERVAL ? DAY
            ORDER BY
                t.creation_date DESC',
            $user_id,
            \pc\datetime_now(),
            $this->ttl);

        foreach($data as $row) {
            if($row['token'] == \pc\bcrypt_hash($token, $row['token'])) {
                return $row;
            }
        }
    }

    public function delete($token_id) {
        $this->exec('
            DELETE FROM
                ' . $this->table_name . '
            WHERE
                token_id = ?',
            $token_id);
    }    

    public function prune() {
        $this->exec('
            DELETE FROM
                ' . $this->table_name . '
            WHERE
                creation_date < ? - INTERVAL ? DAY',
            \pc\datetime_now(),
            $this->ttl);
    }
}
