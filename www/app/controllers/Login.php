<?php

/*
 * The Login Controller
 * author: typefo
 * e-mail: typefo@qq.com
 */

use Tool\Filter;

class LoginController extends Yaf\Controller_Abstract {
    public function indexAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'];

        // Check login action
        if ($request->isPost()) {
            $data = $request->getPost();
            $login = new LoginModel($data);

            // Verify username and password
            if ($login->verify()) {
                $session = Yaf\Session::getInstance();
                $session->set('login', true);
                $response->setRedirect($url . '/cdr');
                $response->response();
                return false;
            }
        }

        $response->setRedirect($url);
        $response->response();
        return false;
    }
}


