<?php

/*
 * The api interface
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

// load configure file
require('../config.php');

try {

    // Initialize mysql database
    $db = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

    // get post json data
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
        $rep = isset($data['variables']) ? $data['variables'] : '';
        $rpf = isset($rep['rpf']) ? $rep['rpf'] : 'unknown';
        $uuid = isset($rep['uuid']) ? $rep['uuid'] : 'unknown';
        $src_ip = isset($rep['sip_network_ip']) ? $rep['sip_network_ip'] : '0.0.0.0';
        $caller = isset($rep['sip_from_user']) ? $rep['sip_from_user'] : 'unknown';
        $called = isset($rep['called']) ? $rep['called']：'unknown';
        $duration = isset($rep['billsec'])) ? intval($rep['billsec']) : 0;
        $file = date('Y/m/d/', intval($rep['start_epoch'])) . $caller . '-' . $called . '-' . $uuid . '.wav';
        $create_time = isset($rep['start_stamp'])) ? urldecode($rep['start_stamp']) : '1970-01-01 08:00:00';

        if ($duration > 0) {
            $db->query("INSERT INTO cdr(caller, called, duration, src_ip, rpf, file, create_time) values('$caller', '$called', $duration, '$src_ip', '$rpf', '$file', '$create_time')");
        }
    } else {
        error_log('php parse cdr application/json data failure', 0);
    }

    // close mysql connection
    $db = null;
} catch (PDOException $e) {
    error_log($e->getMessage(), 0);
}
