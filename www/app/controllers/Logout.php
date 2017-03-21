<?php

/*
 * The Logout Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class LogoutController extends Yaf\Controller_Abstract {

	public function indexAction() {
	    $session = Yaf\Session::getInstance();
	    $session->del('login');
	    $reponse = $this->getResponse();
        $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'];
	    $reponse->setRedirect($url);
	    $reponse->response();
	    return false;
	}
}


