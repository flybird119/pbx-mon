<?php

/*
 * The Gateway Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;
use Esl\ESLconnection;

class GatewayModel {

    public $db   = null;
    private $table = 'external';
    
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
            if (isset($data['prefix'], $data['ip'], $data['port'], $data['description'])) {
                $prefix = Filter::alpha($data['prefix']);
                $ip = Filter::ip($data['ip']);
                $port = Filter::port($data['port'], 5060);
                $description = Filter::string($data['description'], 'No description');

                if ($prefix && $ip && $port && $description) {
                    $sql = 'UPDATE ' . $this->table . ' SET prefix = :prefix, ip = :ip, port = :port, description = :description WHERE id = :id';
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam(':id', $id, PDO::PARAM_INT);
                    $sth->bindParam(':prefix', $prefix, PDO::PARAM_STR);
                    $sth->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $sth->bindParam(':port', $port, PDO::PARAM_INT);
                    $sth->bindParam(':description', $description, PDO::PARAM_STR);

                    if ($sth->execute()) {
                        if($this->regenAcl() && $this->regenPlan()){
                            sleep(1);
                            $this->reloadAcl();
                            sleep(1);
                            $this->reloadXml();
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
            $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ' . $id;
            $success = $this->db->query($sql);
            if ($success) {
                // regenerate the configuration files
                if($this->regenAcl() && $this->regenPlan()){
                    sleep(1);
                    $this->reloadAcl();
                    sleep(1);
                    $this->reloadXml();
                    return true;
                }
            }
        }

        return false;
    }
    
    public function create(array $data = null) {
        if ($this->db) {
            if (isset($data['prefix'], $data['ip'], $data['port'], $data['description'])) {
                $prefix = Filter::alpha($data['prefix']);
                $ip = Filter::ip($data['ip']);
                $port = Filter::port($data['port'], 5060);
                $description = Filter::string($data['description'], 'No description');

                if ($prefix && $ip && $port && $description) {
                    $sql = 'INSERT INTO ' . $this->table . '(prefix, ip, port, description) VALUES(:prefix, :ip, :port, :description)';
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam(':prefix', $prefix, PDO::PARAM_STR);
                    $sth->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $sth->bindParam(':port', $port, PDO::PARAM_INT);
                    $sth->bindParam(':description', $description, PDO::PARAM_STR);

                    if($sth->execute()) {
                        if($this->regenAcl() && $this->regenPlan()){
                            sleep(1);
                            $this->reloadAcl();
                            sleep(1);
                            $this->reloadXml();
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
                $file = '/usr/local/freeswitch/conf/acl/external.xml';
                if (is_writable($file)) {
                    $xml = '<list name="external" default="deny">' . "\n";
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
                }

                error_log('Cannot write file ' . $file . ' permission denied');
            }
        }

        return false;
    }

    public function regenPlan() {
        if ($this->db) {
            $result = $this->getAll();
            if (count($result) > 0) {
                $file = '/usr/local/freeswitch/conf/dialplan/default.xml';
                if (is_writable($file)) {
                    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                    $xml .= '<include>' . "\n";
                    $xml .= '  <context name="default">' . "\n";
                    $xml .= '    <extension name="unloop">' . "\n";
                    $xml .= '      <condition field="${unroll_loops}" expression="^true$"/>' . "\n";
                    $xml .= '      <condition field="${sip_looped_call}" expression="^true$">' . "\n";
                    $xml .= '        <action application="deflect" data="${destination_number}"/>' . "\n";
                    $xml .= '      </condition>' . "\n";
                    $xml .= '    </extension>' . "\n\n";

                    foreach ($result as $obj) {
                        $xml .= '    <extension name="' . $obj['prefix'] . '">' . "\n";
                        $xml .= '      <condition field="destination_number" expression="^' . $obj['prefix'] . '(.*)$">' . "\n";
                        $xml .= '        <action application="set" data="rpf=' . $obj['prefix']. '"/>' . "\n";
                        $xml .= '        <action application="set" data="called=$1"/>' . "\n";
                        $xml .= '        <action application="set" data="call_timeout=60"/>' . "\n";
                        $xml .= '        <action application="set" data="ringback=${cn-ring}"/>' . "\n";
                        $xml .= '        <action application="set" data="RECORD_STEREO=false"/>' . "\n";
                        $xml .= '        <action application="set" data="RECORD_ANSWER_REQ=true"/>' . "\n";
                        $xml .= '        <action application="record_session" data="/var/record/${strftime(%Y/%m/%d}/${caller_id_number}-${called}-${uuid}.wav"/>' . "\n";
                        $xml .= '        <action application="bridge" data="sofia/external/$1@' . $obj['ip'] . ':' . $obj['port'] . '"/>' . "\n";
                        $xml .= '        <action application="hangup"/>' . "\n";
                        $xml .= '        </condition>' . "\n";
                        $xml .= '    </extension>' . "\n";
                    }
                    
                    $xml .= '  </context>' . "\n";
                    $xml .= '</include>' . "\n";
                
                    $fp = fopen($file, "w");
                    if ($fp) {
                        fwrite($fp, $xml);
                        fclose($fp);
                        return true;
                    }
                }

                error_log('Cannot write file ' . $file . ' permission denied');
            }
        }

        return false;
    }

    public function reloadAcl() {
        if ($this->eslCmd('bgapi reloadacl')) {
            return true;
        }

        return false;
    }

    public function reloadXml() {
        if ($this->eslCmd('bgapi reloadxml')) {
            return true;
        }

        return false;
    }

    public function eslCmd($cmd = null) {
        if ($cmd && is_string($cmd)) {
            $config = Yaf\Registry::get('config');

            // conection to freeswitch
            $esl = new ESLconnection($config->esl->host, $config->esl->port, $config->esl->password);

            if ($esl) {
                // exec reloadacl command
                $esl->send($cmd);
                // close esl connection
                $esl->disconnect();
                return true;
            }
            
            error_log('esl cannot connect to freeswitch', 0);
        }

        return false;
    }
}
