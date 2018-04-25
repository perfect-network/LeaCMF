<?php

namespace app\http\middleware;

use app\common\library\Auth;
use app\common\library\Y;
use Firebase\JWT\JWT;
use think\Container;

class ApiAuth
{
    public function handle($request, \Closure $next)
    {
        //验证登录
        $auth = Auth::instance();
        $jwt  = $request->header('Authorization');
        try {
            $jwt = (array)JWT::decode($jwt, env('APP_SECRET'), ['HS256']);
        } catch (\Exception $e) {
            return Y::json(403, $e->getMessage());
        }

        if ($jwt['exp'] > time()) {
            $user = $auth->user($jwt);
        }

        //注入用户
        Container::getInstance()->bind('auth', $auth);
        Container::getInstance()->bind('user', function () use ($auth) {
            return $auth->user();
        });

        if (!$auth->checkPublicUrl()) {
            if ($jwt['exp'] < time()) {
                return Y::json(401, '登录已过期');
            }

            if ($user['token'] != $jwt['token']) {
                return Y::json(401, '用户验证失败，请重新登录');
            }
        }
    }
}
