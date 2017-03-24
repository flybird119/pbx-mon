<?php

/*
 * The Gateway Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class GatewayController extends Yaf\Controller_Abstract {

    public function statusAction() {
        $gateway = new GatewayModel();
        $this->getView()->assign("data", $gateway->getAll());
        return true;
	}

    public function createAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $gateway = new GatewayModel();
            $gateway->create($request->getPost());
            $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/gateway/status';
            $response = $this->getResponse();
            $response->setRedirect($url);
            $response->response();
            return false;
        }
        
        return true;
    }

    public function editAction() {
        $request = $this->getRequest();
        $gateway = new GatewayModel();

        if ($request->isPost()) {
            $gateway->change($request->getQuery('id'), $request->getPost());
            $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/gateway/status';
            $response = $this->getResponse();
            $response->setRedirect($url);
            $response->response();
            return false;
        }
        
        $this->getView()->assign('data', $gateway->get($request->getQuery('id')));
        return true;
    }

    public function deleteAction() {
        $id = $this->getRequest()->getQuery('id');
        $gateway = new GatewayModel();

        $gateway->delete($id);
        
        return false;
    }
}


