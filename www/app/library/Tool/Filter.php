<?php

/*
 * The Content Filter
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

namespace Tool;

class Filter {

    static public function alpha($value = null, $defval = null) {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        if ($value) {
            return preg_replace('/[^a-zA-Z0-9]/', '', $value);
        }

        if ($defval) {
            return $defval;
        }
        
        return false;
    }

    static public function number($value = null) {
        return intval($value);
    }

    static public function string($value = null, $defval = null, $min = 0, $max = PHP_INT_MAX) {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        if ($value) {
            $len = mb_strlen($value);
            if ($len > $min && $len < $max) {
                return $value;
            }
        }

        if ($defval) {
            return $defval;
        }

        return false;
    }
    
    static public function dateTime($value = null, $defval = null) {
        if (is_string($value)) {
            $format = 'Y-m-d H:i:s';
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) === $value) {
                return $value;
            }
        }

        if ($defval) {
            return $defval;
        }

        return false;
    }

    static public function ip($value = null, $defval = null) {
        $value = filter_var($value, FILTER_VALIDATE_IP);
        if ($value) {
            return $value;
        }

        if ($defval) {
            return $defval;
        }
        
        return false;
    }

    static public function port($value = null, $defval = null) {
        $value = intval($value);
        if ($value > 0 && $value < 65535) {
            return $value;
        }

        if ($defval) {
            return $defval;
        }

        return false;
    }
}
