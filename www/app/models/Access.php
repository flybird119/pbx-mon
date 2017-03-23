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
        $result = array();

        if ($this->db && is_numeric($id) && $id > 0) {
            $sth = $this->db->prepare('SELECT * FROM internal WHERE id = :id LIMIT 1');
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch();
        }

        return $result;
    }

    public function getAll() {
        $result = array();
        if ($this->db) {
            $result = $this->db->query('SELECT * FROM internal ORDER BY id');
        }

        return $result;
    }
    
    public function change($id = null, array $data = null) {

    }


    public function delete($id = null) {

    }
    
    public function create(array $data = null) {

    }
}
