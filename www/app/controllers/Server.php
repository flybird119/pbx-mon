<?php

/*
 * The Report Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class ServerController extends Yaf\Controller_Abstract {

    public function statusAction() {
        $server = new ServerModel();

        $this->getView()->assign("status", $server->sysInfo());
        return true;
	}
}

