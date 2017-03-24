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
        return true;
    }

    public function deleteAction() {
        $id = intval($this->getRequest()->getQuery('id'));
        $gateway = new GatewayModel();

        if ($gateway->isExist($id)) {
            $gateway->delete($id);
        }

        return false;
    }
}


