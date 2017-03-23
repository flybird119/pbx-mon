<?php

/*
 * The Access Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;

class AccessModel {
    public $db   = null;
    
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
        if ($this->db && $this->isExist($id)){
            $id = intval($id);
            $success = $this->db->query('DELETE FROM internal WHERE id = ' . $id . '');
            if ($success) {
                // regenerate the configuration files
                if($this->regenXml()) {
                    // reload acl list
                    $this->reloadAcl();
                }
            }
        }
    }
    
    public function create(array $data = null) {
        if ($this->db) {
            if (isset($data['name'], $data['ip'], $data['port'], $data['description'])) {
                $name = Filter::alpha($data['name'], 'unknown');
                $ip = Filter::ip($data['ip']);
                $port = Filter::port($data['port'], 5060);
                $description = Filter::string($data['description'], 'No description');

                if ($name && $ip && $port && $description) {
                    $sth = $this->db->prepare('INSERT INTO internal(name, ip, port, description) VALUES(:name, :ip, :port, :description)');
                    $sth->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $sth->bindParam(':port', $ip, PDO::PARAM_INT);
                    $sth->bindParam(':description', $ip, PDO::PARAM_STR);

                    if($sth->execute()) {
                        if($this->regenXml()){
                            $this->reloadAcl();
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function isExist($id = null) {
        $id = intval($id);
        if ($id > 0 && $this->db) {
            $result = $this->db->query('SELECT id FROM internal WHERE id = ' . $id . ' LIMIT 1');
            if (count($result) > 0) {
                return true;
            }
        }

        return false;
    }

    public function regenXml() {
        if ($this->db) {
            $result = $this->getAll();
            if (count($result) > 0) {
                $file = '/usr/local/freeswitch/conf/acl/internal.xml';
                if (is_writable($file)) {
                    $xml = '<list name="internal" default="deny">' . "\n";
                    foreach ($result as $obj) {
                        $xml .= '  <node type="allow" cidr="' . $obj['ip'] . '/32"/>' . "\n";
                    }
                    $xml .= '</list>' . "\n";

                
                    $fp = fopen($file, "w");
                    if ($fp) {
                        fwrite($fp, $xml);
                        fclose($fp);
                        return true;
                    }
                } else {
                    error_log('Cannot write file ' . $file . 'permission denied');
                }
            }
        }

        return false;
    }

    public function reloadAcl() {
        $config = Yaf\Registry::get('config');
        Yaf\loader::import("library/Esl/ESLconnection.php");

        // conection to freeswitch
        $esl = new ESLconnection($config->esl->host, $config->esl->port, $config->esl->password);

        if ($esl) {
            // exec reloadacl command
            $esl->send('bgapi reloadacl');
        } else {
            error_log('esl cannot connect to freeswitch', 0);
        }

        // close esl connection
        if ($esl) {
            $esl->disconnect();
        }
    }
}
