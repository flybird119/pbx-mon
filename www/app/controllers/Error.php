<?php

/*
 * The Error Controller
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class ErrorController extends Yaf\Controller_Abstract {
    public function errorAction($exception) {
        //echo $exception->getMessage();
        echo '<center style="margin-top:100px;font-size:24px">Oops! 404 Not Found</center>';
        return false;
    }
}
