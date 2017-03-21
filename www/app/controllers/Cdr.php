<?php

/*
 * The mod json cdr request
 * author: typefo
 * e-mail: typefo@qq.com
 */

use Tool\Filter;

class CdrController extends Yaf\Controller_Abstract {

    public function indexAction() {
        $request = $this->getRequest();

        $cdr = new CdrModel();
	
        $where = $request->getPost();

        if ($request->isPost()) {
            $this->getView()->assign("data", $cdr->query($where));
            $this->getView()->assign("where", $this->check($where));
        } else {
            $this->getView()->assign("data", $cdr->ToDay());
            $this->getView()->assign("where", $this->check($where));
        }

        return true;
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
