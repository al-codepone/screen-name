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
        $this->exec(sprintf('
            INSERT INTO %s
                (user_id, token, data, creation_date)
            VALUES
                (%d, "%s", "%s", "%s")',
            $this->table_name,
            $user_id,
            $this->esc(\pc\bcrypt_hash($token, BCRYPT_COST)),
            $this->esc($data),
            \pc\datetime_now()));
    }

    public function get($user_id, $token) {
        $query = sprintf('
            SELECT
                t.token_id,
                t.token,
                t.data,
                u.user_id,
                u.username
            FROM
                %s t
            JOIN
                tuser u
            ON
                t.user_id = u.user_id
            WHERE
                t.user_id = %d AND
                t.creation_date > "%s" - INTERVAL %d DAY
            ORDER BY
                t.creation_date DESC',
            $this->table_name,
            $user_id,
            \pc\datetime_now(),
            $this->ttl);

        foreach($this->query($query) as $row) {
            if($row['token'] == \pc\bcrypt_hash($token, $row['token'])) {
                return $row;
            }
        }
    }

    public function delete($token_id) {
        $this->exec(sprintf('
            DELETE FROM
                %s
            WHERE
                token_id = %d',
            $this->table_name,
            $token_id));
    }    

    public function prune() {
        $this->exec(sprintf('
            DELETE FROM
                %s
            WHERE
                creation_date < "%s" - INTERVAL %d DAY',
            $this->table_name,
            \pc\datetime_now(),
            $this->ttl));
    }
}
