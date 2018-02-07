<?php

namespace app\admin\controller;

use think\db\Query;

class IndexController extends BaseController
{
    public function index()
    {
        $mysql_version             = (new Query())->query('select version() as ver');
        $sys_info['mysql_version'] = $mysql_version[0]['ver'];
        $sys_info['domain']        = $_SERVER['HTTP_HOST'];
        $sys_info['web_server']    = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']          = phpversion();
        $sys_info['ip']            = GetHostByName($_SERVER['SERVER_NAME']);
        return view('index', [
            'sys_info' => $sys_info
        ]);
    }

    public function system()
    {
        $mode   = "/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
        $string = shell_exec("more /proc/stat");
        preg_match_all($mode, $string, $arr);
        //print_r($arr);
        $total1 = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[5][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time1  = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];

        sleep(1);
        $string = shell_exec("more /proc/stat");
        preg_match_all($mode, $string, $arr);
        $total2 = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[5][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time2  = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time   = $time2 - $time1;
        $total  = $total2 - $total1;
        //echo "CPU amount is: ".$num;
        $percent   = bcdiv($time, $total, 5);
        $cpu_usage = round($percent * 100, 3) . '%';

        $str  = shell_exec("more /proc/meminfo");
        $mode = "/(.+):\s*([0-9]+)/";
        preg_match_all($mode, $str, $arr);
        $pr = bcdiv($arr[2][1], $arr[2][0], 5);
        $pr = round($pr * 100, 3) . '%';

        $this->success('success', '', [
            'cpu_usage' => $cpu_usage,
            'mem_usage' => $pr
        ]);
    }
}
