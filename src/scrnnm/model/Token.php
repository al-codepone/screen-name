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
            create table ' . $this->table_name . ' (
                token_id int unsigned auto_increment primary key,
                user_id int unsigned not null,
                token varchar(255) not null,
                data varchar(255),
                creation_date datetime not null)');
    }

    public function create($user_id, $token, $data = '') {
        $this->exec('
            insert into ' . $this->table_name . '
                (user_id, token, data, creation_date)
            values
                (?, ?, ?, ?)',
            $user_id,
            password_hash($token, PASSWORD_DEFAULT),
            $data,
            \pc\datetime_now());
    }

    public function get($user_id, $token) {
        $data = $this->query('
            select
                t.token_id,
                t.token,
                t.data,
                u.user_id,
                u.username
            from
                ' . $this->table_name . ' t
            join
                tuser u
            on
                t.user_id = u.user_id
            where
                t.user_id = ? and
                t.creation_date > ? - interval ? day
            order by
                t.creation_date desc',
            $user_id,
            \pc\datetime_now(),
            $this->ttl);

        foreach($data as $row) {
            if(password_verify($token, $row['token'])) {
                return $row;
            }
        }
    }

    public function delete($token_id) {
        $this->exec('
            delete from
                ' . $this->table_name . '
            where
                token_id = ?',
            $token_id);
    }    

    public function prune() {
        $this->exec('
            delete from
                ' . $this->table_name . '
            where
                creation_date < ? - INTERVAL ? DAY',
            \pc\datetime_now(),
            $this->ttl);
    }
}
