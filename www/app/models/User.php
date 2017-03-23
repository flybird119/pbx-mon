<?php

/*
 * The User Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;

class UserModel {

    public  $db = null;
    private $username = null;

    public function __construct($username) {
        if (is_string($username) && mb_strlen($username) > 0) {
            $this->username = Filter::alpha($username);
            $this->db = Yaf\Registry::get('db');
        }
    }

    public function changePassword($oldpassword = null, $newpassword = null) {
        if ($this->db && $this->username) {
            if (is_string($oldpassword) && is_string($newpassword) && ($oldpassword != $newpassword) && mb_strlen($newpassword) > 6) {
                $oldpassword = sha1(md5($oldpassword));
                $newpassword = sha1(md5($newpassword));

                if ($this->isExist()) {
                    $sth = $this->db->prepare('SELECT password FROM account WHERE username = :username LIMIT 1');
                    $sth->bindParam(':username', $this->username, PDO::PARAM_STR);
                    $sth->execute();
                    $result = $sth->fetch();
                    if (is_array($result) && count($result) > 0) {
                        if ($result['password'] === $oldpassword) {
                            $sth = $this->db->prepare('UPDATE account SET password = :password WHERE username = :username');
                            $sth->bindParam(':username', $this->username, PDO::PARAM_STR);
                            $sth->bindParam(':password', $newpassword, PDO::PARAM_STR);
                            return $sth->execute();
                        }
                    }
                }
            }
        }

        return false;
    }

    public function isExist() {
        if ($this->db && $this->username) {
            $sth = $this->db->prepare('SELECT username FROM account WHERE username = :username LIMIT 1');
            $sth->bindParam(':username', $this->username, PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetch();

            if (is_array($result) && count($result) > 0) {
                return true;
            }
        }

        return false;
    }

}
