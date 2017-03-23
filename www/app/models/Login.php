<?php

/*
 * The Login Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;

class LoginModel {
    public  $db = null;
    private $username = null;
    private $password = null;
      
    public function __construct(array $data = null) {
        if (isset($data['username']) && is_string($data['username']) && mb_strlen($data['username']) > 0) {
            $this->username = Filter::alpha($data['username']);
        }

        if (isset($data['password']) && is_string($data['password']) && mb_strlen($data['password']) > 5) {
            $this->password = sha1(md5($data['password']));
        }

        $this->db = Yaf\Registry::get('db');
    }

    public function verify() {
        if ($this->db && $this->username && $this->password) {
            $sth = $this->db->prepare('SELECT * FROM account WHERE username = :username AND password = :password LIMIT 1');
            $sth->bindParam(':username', $this->username, PDO::PARAM_STR);
            $sth->bindParam(':password', $this->password, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch();

            if (is_array($result) && count($result) > 0) {
                return true;
            }
        }

        return false;
    }
}
