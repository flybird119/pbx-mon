<?php

/*
 * The Cdr model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;

class CdrModel {

    public $db = null;

    public function __construct() {
        $this->db = Yaf\Registry::get('db');
    }   

    public function query(array $where) {
        if ($this->db) {
            $sql = 'SELECT * FROM cdr WHERE ';

            if (isset($where['caller'])) {
                if (is_string($where['caller']) && mb_strlen($where['caller']) > 0) {
                    $sql .= 'caller = :caller ';		    
                } else {
                    unset($where['caller']);
                }
            }

            if (isset($where['called'])) {
                if (is_string($where['called']) && mb_strlen($where['called']) > 0) {
                    if (isset($where['caller'])) {
                        $sql .= 'AND called = :called ';
                    } else {
                        $sql .= 'called = :called ';
                    }
                } else {
                    unset($where['called']);
                }
            }

            if (isset($where['caller']) || isset($where['called'])) {
                $sql .= 'AND duration > :duration AND create_time BETWEEN :begin AND :end ORDER BY id DESC LIMIT 150';
            } else {
                $sql .= 'duration > :duration AND create_time BETWEEN :begin AND :end ORDER BY id DESC LIMIT 150';
            }

            $sth = $this->db->prepare($sql);

            if (isset($where['caller'])) {
                $caller = Filter::alpha($where['caller']);
    	        $sth->bindParam(':caller', $caller, PDO::PARAM_STR);
            }

            if (isset($where['called'])) {
                $called = Filter::alpha($where['called']);
                $sth->bindParam(':called', $called, PDO::PARAM_STR);
            }

            if (isset($where['duration'])) {
                $duration = Filter::number($where['duration']);
                $sth->bindParam(':duration', $duration, PDO::PARAM_INT);
            } else {
    	        $duration = 0;
                $sth->bindParam(':duration', $duration, PDO::PARAM_INT);
            }

            if (isset($where['begin'])) {
                $begin = Filter::dateTime($where['begin']);
                $sth->bindParam(':begin', $begin, PDO::PARAM_STR);
            } else {
                $begin = date('Y-m-d 08:00:00');
                $sth->bindParam(':begin', $begin, PDO::PARAM_STR);
            }

            if (isset($where['end'])) {
                $end = Filter::dateTime($where['end']);
                $sth->bindParam(':end', $end, PDO::PARAM_STR);
            } else {
                $begin = date('Y-m-d 20:00:00');
                $sth->bindParam(':end', $end, PDO::PARAM_STR);
            }

            $sth->execute();
            return $sth->fetchAll();
        }
        
	
    }

    public function ToDay() {
        if ($this->db) {
            $where['begin'] = date('Y-m-d 08:00:00');
            $where['end']   = date('Y-m-d 20:00:00');
            return $this->query($where);
        }

        return null;
    }

    public function __destruct() {
        $this->db = null;
    }
}
