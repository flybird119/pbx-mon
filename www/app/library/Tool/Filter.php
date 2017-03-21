<?php

/*
 * The Content Filter
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

namespace Tool;

class Filter {

    static public function alpha($value) {
        if ($value = filter_var($value, FILTER_SANITIZE_STRING)) {
            return preg_replace('/[^a-zA-Z0-9]/', '', $value);
        }

        return '';
    }

    static public function number($value) {
        return intval($value);
    }
    
    static public function dateTime($value) {
        if (is_string($value)) {
            $format = 'Y-m-d H:i:s';
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) === $value) {
                return $value;
            }
        }

        return date('Y-m-d H:i:s');
    }


}
