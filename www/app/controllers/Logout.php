<?php

/*
 * The mod json cdr request
 * author: typefo
 * e-mail: typefo@qq.com
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


