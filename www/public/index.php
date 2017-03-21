<?php

/*
 * application
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

define('APP_PATH', dirname(dirname(__FILE__)));
$application = new Yaf\Application(APP_PATH . "/conf/application.ini");
$application->bootstrap()->run();
