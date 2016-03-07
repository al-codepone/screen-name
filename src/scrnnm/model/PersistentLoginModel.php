<?php

namespace scrnnm\model;

use pjsql\DatabaseHandle;

class PersistentLoginModel extends TokenModel {
    public function __construct(DatabaseHandle $database_handle) {
        parent::__construct(
            $database_handle,
            'tpersistent_login_token',
            TTL_PERSISTENT_LOGIN);
    }

    public function create($user_id) {
        $token = \pc\sha1_token();
        parent::create($user_id, $token);
        setcookie(COOKIE_PERSISTENT_LOGIN, "$user_id.$token",
            time() + 60*60*24*TTL_PERSISTENT_LOGIN);
    }

    public function get() {
        if($_COOKIE[COOKIE_PERSISTENT_LOGIN]) {
            list($user_id, $token) = explode('.', $_COOKIE[COOKIE_PERSISTENT_LOGIN]);
            return parent::get($user_id, $token);
        }
    }
}
