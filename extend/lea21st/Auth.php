<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/30
 * Time: 14:00
 */

namespace lea21st;

use lea21st\jwt\JWT;
use think\Db;
use think\facade\Request;

class Auth
{

    /**
     * user
     * @var
     */
    protected $user;


    protected $config;


    /**
     * user_id
     * @var
     */
    protected $user_id;

    /**
     * @var object 对象实例
     */
    protected static $instance;


    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct()
    {
        if ($config = \think\facade\Config::get('auth.')) {
            $this->config = $config;
        }
    }

    /**
     * 初始化
     * @param array $options
     * @return object|static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 生产token
     * @param $user_id
     * @return string
     */
    public function token($user_id)
    {
        $time    = time();
        $JWT_TTL = isset($this->config['JWT_TTL']) ? $this->config['JWT_TTL'] : 7 * 24 * 60;
        $token   = sha1(uniqid());
        $jwt     = [
            "user_id" => $user_id,
            "iat"     => $time,
            "token"   => $token,
            "exp"     => $time + $JWT_TTL
        ];
        if (Db::name('user')->where('id', $user_id)->setField('token', $token)) {
            return JWT::encode($jwt, env('APP_SECRET'));
        }

    }

    /**
     * 校验url，是否需要用户验证
     * @return bool
     */
    public function checkPublicUrl()
    {
        $urls = $this->config['public_url'];
        $path = '/' . str_replace('.', '/', strtolower(Request::module() . '/' . Request::controller() . '/' . Request::action()));
        //判断是否需要验证
        foreach ($urls as $val) {
            if ($path == $val) return true;
            $position = strspn($path ^ $val, "\0");
            $str      = substr($val, $position);
            if ($str == '*') return true;
        }
        return false;
    }

    /**
     * 当前登录用户
     * @param array $jwt
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user($jwt = [])
    {
        if ($jwt) {
            $user = Db::name('user')->cache('user_info_' . $jwt['user_id'], 600)->find($jwt['user_id']);
            unset($user['password']);
            $this->user    = $user;
            $this->user_id = $jwt['user_id'];
        }
        return $this->user;
    }

    /**
     * 获取用户id
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

}
