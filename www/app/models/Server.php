<?php

/*
 * The User Model
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class UserModel {

    public function sysInfo($esl = null) {
        $status['uptime'] = $this->getUptime();
        $status['cpuinfo'] = $this->getCpuInfo();
        $status['memory'] = $this->getMemory();
        $status['hard'] = $this->getDisk();
        $status['uname'] = $this->getUname();

        return $status;
    }

    public function getUptime() {
        $str = "";
        $uptime = "";
    
        if (($str = @file("/proc/uptime")) === false) {
            return "";
        }
    
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        if ($days !== 0) {
            $uptime = $days."天";
        }
        if ($hours !== 0) {
            $uptime .= $hours."小时";
        }

        $uptime .= $min."分钟";

        return $uptime;
    }
    
    public function getCpuInfo() {
        if (($str = @file("/proc/cpuinfo")) === false) {
            return false;
        }
    
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);

        if (false !== is_array($model[1])) {
            $core = sizeof($model[1]);
            $cpu = $model[1][0].' x '.$core.'核';
            return $cpu;
        }

        return "Unknown";
    }
    
    public function getDisk() {
        $total = round(@disk_total_space(".")/(1024*1024*1024),3); //总
        $avail = round(@disk_free_space(".")/(1024*1024*1024),3); //可用
        $use = $total - $avail; //已用
        $percentage = (floatval($total) != 0) ? round($avail / $total * 100, 0) : 0;

        return ['total' => $total, 'avail' => $avail, 'use' => $use, 'percentage' => $percentage];
    }
    
    public function getLoadavg() {
        if (($str = @file("/proc/loadavg")) === false) {
            return 'Unknown';
        }

        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 4);
        $loadavg = implode(" ", $str[0]);

        return $loadavg;
    }
    
    public function getMemory() {
        if (false === ($str = @file("/proc/meminfo"))) {
            return ['total' => 0, 'free' => 0, 'use' => 0, 'percentage' => 0];
        }
    
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);
    
        $total = round($buf[1][0] / 1024, 2);
        $free = round($buf[2][0] / 1024, 2);
        $buffers = round($buffers[1][0] / 1024, 2);
        $cached = round($buf[3][0] / 1024, 2);
        $use = $total - $free - $cached - $buffers; //真实内存使用
        $percentage = (floatval($total) != 0) ? round($use / $total * 100, 0) : 0; //真实内存使用率

        return ['total' => $total, 'free' => $free, 'use' => $use, 'percentage' => $percentage];
    }
    
    public function getUname() {
        return php_uname();
    }
}
