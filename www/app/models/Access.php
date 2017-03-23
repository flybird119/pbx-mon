<?php

/*
 * The Access Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class AccessModel {
    public $db   = null;
    public $id   = null;
    public $name = null;
    public $ip   = null;
    public $port = null;
    public $description = null;
    
    public function __construct() {
        $this->db = Yaf\Registry::get('db');
    }

    public function get($id = null) {
        if ($this->db && is_numeric($id) && $id > 0) {
            $sth = $this->db->prepare('SELECT * FROM internal WHERE id = :id LIMIT 1');
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch();
            if (is_array($result) && count($result) > 0) {
                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->ip = $result['ip'];
                $this->port = $result['port'];
                $this->description = $result['description'];
                return true;
            }
        }

        return false;
    }

    public function change($id = null, array $data = null) {

    }


    public function delete($id = null) {

    }
    
    public function create(array $data = null) {

    }
}
