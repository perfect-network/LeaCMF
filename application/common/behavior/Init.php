<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/2/5
 * Time: 14:35
 */

namespace app\common\behavior;

use lea21st\Config;
use lea21st\RBAC;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\facade\Response;

class Init
{
    public function run()
    {
        //注入配置项
        Config::config();

        if (Request::module() != 'admin') {
            return;
        }

        $auth = RBAC::instance();

        //ip限制
        if (config('admin_allow_ip')) {
            if (!in_array(Request::ip(), explode(',', config('admin_allow_ip')))) {
                exit('forbidden');
            }
        }

        //验证登录
        if (!$auth->checkPublicUrl()) {
            if (!$auth->isLogin()) {
                throw new HttpResponseException(redirect('public/login'));
            }

            //验证权限
            if (!$auth->check()) {
                $data     = [
                    'code' => -1,
                    'msg'  => '您没有权限操作',
                    'data' => [],
                    'url'  => '',
                    'wait' => 3,
                ];
                $response = Response::create($data, Request::isAjax() ? 'json' : 'jump')->options(['jump_template' => config('dispatch_error_tmpl')]);
                throw new HttpResponseException($response);
            }
        }
    }
}