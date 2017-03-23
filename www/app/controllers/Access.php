<?php

/*
 * The Access Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class AccessController extends Yaf\Controller_Abstract {

    public function statusAction() {
        $access = new AccessModel();
        $this->getView()->assign("data", $access->getAll());
        return true;
	}

    public function createAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $access = new AccessModel();
            $access->create($request->getPost());
            $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/access/status';
            $this->getResponse()->setRedirect($url)->response();
            return false;
        }
        
        return true;
    }

    public function editAction() {
        return true;
    }

    public function deleteAction() {
        $id = intval($this->getRequest()->getQuery('id'));
        $access = new AccessModel();

        if ($access->isExist($id)) {
            $access->delete($id);
        }

        return false;
    }
}


