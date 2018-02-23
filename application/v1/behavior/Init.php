<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/2/22
 * Time: 17:41
 */

namespace app\v1\behavior;

use lea21st\Auth;
use lea21st\jwt\JWT;
use lea21st\Y;
use think\facade\Request;

class Init
{
    public function run()
    {
        //验证登录
        $auth = Auth::instance();
        if (!$auth->checkPublicUrl()) {
            $jwt = Request::header('Authorization');
            try {
                $jwt = (array)JWT::decode($jwt, env('APP_SECRET'), ['HS256']);
            } catch (\Exception $e) {
                return Y::json(401, $e->getMessage());
            }
            if ($jwt['exp'] < time()) {
                return Y::json(401.1, '登录已过期');
            }
            $user = $auth->user($jwt);
            if ($user['token'] != $jwt['token']) {
                return Y::json(401.1, '登录已过期');
            }
        }



    }
}