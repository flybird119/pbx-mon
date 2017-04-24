<?php

/*
 * The api interface
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

// load configure file
require('../config.php');

try {
    // get post json data
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
    	// Initialize mysql connection
    	$db = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

    	// Variable processing
        $req = isset($data['variables']) ? $data['variables'] : '';
        $rpf = isset($req['rpf']) ? $req['rpf'] : 'unknown';
        $uuid = isset($req['uuid']) ? $req['uuid'] : 'unknown';
        $src_ip = isset($req['sip_network_ip']) ? $req['sip_network_ip'] : '0.0.0.0';
        $caller = isset($req['sip_from_user']) ? $req['sip_from_user'] : 'unknown';
        $called = isset($req['called']) ? $req['called']：'unknown';
        $duration = isset($req['billsec'])) ? intval($req['billsec']) : 0;
        $file = date('Y/m/d/', intval($req['start_epoch'])) . $caller . '-' . $called . '-' . $uuid . '.wav';
        $create_time = isset($req['start_stamp'])) ? urldecode($req['start_stamp']) : '1970-01-01 08:00:00';

        if ($duration > 0) {
            $db->query("INSERT INTO cdr(caller, called, duration, src_ip, rpf, file, create_time) values('$caller', '$called', $duration, '$src_ip', '$rpf', '$file', '$create_time')");
        }

        // Close mysql connection
    	$db = null;
    } else {
        error_log('php parse cdr application/json data failure', 0);
    }

} catch (PDOException $e) {
    error_log($e->getMessage(), 0);
}
