<?php

/*
 * The Access Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;
use Esl\ESLconnection;

class AccessModel {
    public $db   = null;
    private $table = 'internal';
    
    public function __construct() {
        $this->db = Yaf\Registry::get('db');
    }

    public function get($id = null) {
        $id = intval($id);
        if ($id > 0 && $this->db) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
            return $sth->fetch();
        }

        return null;
    }

    public function getAll() {
        $result = array();
        if ($this->db) {
            $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY id';
            $result = $this->db->query($sql);
        }

        return $result;
    }
    
    public function change($id = null, array $data = null) {
        $id = intval($id);
        if ($id > 0 && $this->db && $this->isExist($id)) {
            if (isset($data['name'], $data['ip'], $data['port'], $data['description'])) {
                $name = Filter::alpha($data['name'], 'unknown');
                $ip = Filter::ip($data['ip']);
                $port = Filter::port($data['port'], 5060);
                $description = Filter::string($data['description'], 'No description');

                if ($name && $ip && $port && $description) {
                    $sql = 'UPDATE ' . $this->table . ' SET name = :name, ip = :ip, port = :port, description = :description WHERE id = :id';
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    $sth->bindParam(':name', $name, PDO::PARAM_STR);
                    $sth->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $sth->bindParam(':port', $port, PDO::PARAM_INT);
                    $sth->bindParam(':description', $description, PDO::PARAM_STR);

                    if ($sth->execute()) {
                        if($this->regenAcl()){
                            sleep(1);
                            $this->reloadAcl();
                            return true;
                        }
                    }
                }
            }

        }

        return false;
    }


    public function delete($id = null) {
        $id = intval($id);
        if ($id > 0 && $this->db && $this->isExist($id)){
            $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ' . $id . '';
            $success = $this->db->query($sql);
            if ($success) {
                // regenerate the configuration files
                if($this->regenAcl()) {
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
                    $sql = 'INSERT INTO ' . $this->table . '(name, ip, port, description) VALUES(:name, :ip, :port, :description)';
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam(':name', $name, PDO::PARAM_STR);
                    $sth->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $sth->bindParam(':port', $port, PDO::PARAM_INT);
                    $sth->bindParam(':description', $description, PDO::PARAM_STR);

                    if($sth->execute()) {
                        if($this->regenXml()){
                            sleep(1);
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
            $sql = 'SELECT id FROM ' . $this->table . ' WHERE id = ' . $id . ' LIMIT 1';
            $result = $this->db->query($sql);
            if (count($result) > 0) {
                return true;
            }
        }

        return false;
    }

    public function regenAcl() {
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
                    error_log('Cannot write file ' . $file . ' permission denied');
                }
            }
        }

        return false;
    }

    public function reloadAcl() {
        $config = Yaf\Registry::get('config');

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
