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
        $rep = $data['variables'];
        $rpf = $rep['rpf'];
        $uuid = $rep['uuid'];
        $src_ip = $rep['sip_network_ip'];
        $caller = $rep['sip_from_user'];
        $called = $rep['called'];
        $duration = intval($rep['billsec']);
        $file = date('Y/m/d/', intval($rep['start_epoch'])) . $caller . '-' . $called . '-' . $uuid . '.wav';
        $create_time = urldecode($rep['start_stamp']);

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
