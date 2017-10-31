<?php

/**
 * 用户注册接口
 * Date: 2016/11/15
 */

require_once dirname(__FILE__) . '/../../Config.php';
require_once SERVICE_PATH . '/Cmd.php';
require_once SERVICE_PATH . '/CmdResp.php';
require_once ROOT_PATH . '/ErrorNo.php';
require_once MODEL_PATH . '/Account.php';

class AccountLoginCmd extends Cmd
{
    // 用户账号对象
    private $account;
    private $appid;
    private $privatekey;
    private $publickey;

    public function __construct()
    {
        $this->account = new Account();
    }

    public function parseInput()
    {
        if (empty($this->req['id'])) {
            return new CmdResp(ERR_REQ_DATA, '没有用户名');
        }
        if (!is_string($this->req['id'])) {
            return new CmdResp(ERR_REQ_DATA, '用户名格式错误');
        }
        $this->account->setUser($this->req['id']);

        if (empty($this->req['pwd'])) {
            return new CmdResp(ERR_REQ_DATA, '请输入密码');
        }
        if (!is_string($this->req['pwd'])) {
            return new CmdResp(ERR_REQ_DATA, '密码格式错误');
        }

        if (isset($this->req['appid']) && (is_int($this->req['appid']) || is_string($this->req['appid']))) {
            $this->appid = strval($this->req['appid']);
        } else {
            $this->appid = DEFAULT_SDK_APP_ID;
        }

        $this->privatekey = KEYS_PATH . '/' . $this->appid . '/private_key';
        $this->publickey = KEYS_PATH . '/' . $this->appid . '/public_key';
        if (!file_exists($this->privatekey) || !file_exists($this->publickey)) {
            return new CmdResp(ERR_REQ_DATA, '缺少参数');
        }

        return new CmdResp(ERR_SUCCESS, '');
    }

    public function handle()
    {
        $account = $this->account;
        $errorMsg = '';

        // 获取用户账号信息
        $ret = $account->getAccountRecordByUserID($errorMsg);
        if ($ret != ERR_SUCCESS) {
            return new CmdResp($ret, $errorMsg);
        }

        // 密码验证
        $ret = $account->authentication($this->req['pwd'], $errorMsg);
        if ($ret != ERR_SUCCESS) {
            return new CmdResp($ret, $errorMsg);
        }

        // 获取sig
        $userSig = $account->getUserSig();
        if (empty($userSig)) {
            $userSig = $account->genUserSig($this->appid, $this->privatekey);
            // 更新对象account的成员userSig
            $account->setUserSig($userSig);
        } else {
            $ret = $account->verifyUserSig($this->appid, $this->publickey);
            if ($ret == 1) //过期重新生成
            {
                $userSig = $account->genUserSig($this->appid, $this->privatekey);
                // 更新对象account的成员userSig
                $account->setUserSig($userSig);
            } elseif ($ret == -1) {
                return new CmdResp(ERR_SERVER, '服务器异常r:gen sig fail');
            }
        }
        if (empty($userSig))
            return new CmdResp(ERR_SERVER, '服务器异常: gen sig fail');

        //        $ret = $account->getState();
        //        if($ret == 1) //已登录
        //        {
        //            $data = array();
        //            $data['userSig'] = $account->getUserSig();
        //            $data['token'] = $account->getToken();
        //            return new CmdResp(ERR_SUCCESS, '', $data);
        //        }

        //获取token
        $token = $account->genToken();
        if (empty($token)) {
            return new CmdResp(ERR_SERVER, '服务器内部错误');
        }
        $account->setToken($token);

        $account->setLoginTime(date('U'));

        //登录，更新DB    
        $ret = $account->login($errorMsg);

        if ($ret != ERR_SUCCESS) {
            return new CmdResp($ret, $errorMsg);
        }

        //更新 app id
        $ret = $account->updateCurrentAppid($errorMsg, $this->appid);

        if ($ret != ERR_SUCCESS) {
            return new CmdResp($ret, $errorMsg);
        } else {
            $data['userSig'] = $userSig;
            $data['token'] = $token;
            return new CmdResp(ERR_SUCCESS, '', $data);
        }
    }
}
