<?php

/*
 * The Cdr Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

use Tool\Filter;

class CdrController extends Yaf\Controller_Abstract {

    public function indexAction() {
        $request = $this->getRequest();

        $cdr = new CdrModel();
	
        $where = $request->getQuery();

        if (isset($where['sub'])) {
            $data = $cdr->query($where);
            $this->getView()->assign("data", $data);
            $this->getView()->assign("where", $this->check($where));
            $this->getView()->assert('last', intval($data[count($data) - 1]['id']));
        } else {
            $this->getView()->assign("data", null);
            $this->getView()->assign("where", null);
        }

        return true;
    }

    public function ajxqueryAction() {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $cdr = new CdrModel();
            $data = $cdr->query($request->getQuery());
            if ($data) {
                $response['status'] = 200;
                $response['message'] = 'success';
                $response['data'] = $data;
                $response['last'] = intval($data[count($data) - 1]['id']);
                header('Content-type: application/json');
                echo json_encode($response);
                return false;
            }
        }
        
        header('Content-type: application/json');
        json_encode(['status' => 400, 'message' => 'Illegal request']);
        
        return false;
    }
    
    public function check(array $data) {
        $where = [];
        foreach ($data as $key => $value) {
            switch ($key) {
	        case 'begin':
                $where['begin'] = htmlspecialchars(Filter::dateTime($value),  ENT_QUOTES );
                break;
            case 'end':
                $where['end'] = htmlspecialchars(Filter::dateTime($value), ENT_QUOTES);
                break;
	        case 'caller':
                $where['caller'] = htmlspecialchars(Filter::alpha($value), ENT_QUOTES);
                break;
            case 'called':
                $where['called'] = htmlspecialchars(Filter::alpha($value), ENT_QUOTES);
                break;
            case 'duration':
                $where['duration'] = Filter::number($value);
                break;
            }
        }

        return $where;
    }
}
