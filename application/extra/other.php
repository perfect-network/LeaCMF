<?php
return [

    //短信接口
    'sms'           => [
        'account'  => 'AA01018',
        'password' => 'AA0101888',
    ],
    //不需要登录的接口
    'no_token_url'  => [
        '/v1/public/*',
        '/v1/file/*',
        '/v1/app/*',
        '/v1/data/*',
    ],
    //极光推送
    'jiguang_push'  => [
        'app_key'       => '11ea4696f196325397673a871',
        'master_secret' => '8ddf0f673c5f1145146f6566',
    ],

    //第三方开放平台登录
    'third'         => [
        'wechat' => [
            'app_id'     => 'wx5dd3fd6c609e8ca2',
            'app_secret' => '3f0dec8762179e15723edeb5ae76138d',
            'callback'   => '',
            'scope'      => 'snsapi_userinfo',
        ],
    ],
    'easemob'       => [
        'org_name'  => '1174170802178711',
        'app_name'  => 'quanminmei',
        'org_admin' => '2915510443@qq.com',
        'app_key'   => '1174170802178711#quanminmei',

        'client_id'     => 'YXA6YDbasHf1EeeUhbNVk0CySQ',
        'client_secret' => 'YXA6arqECMiMU1ntJsuAKtNdUnJDw9c',
    ],
    'medal'         => [
        '1' => '1',
        '2' => '8',
        '3' => '16',
        '4' => '31',
        '5' => '61',
        '6' => '91',
        '7' => '121',
    ],
    'cate_name'     => [
        'course'       => '精品课',
        'special_zone' => '专栏',
        'ting'         => '听书产品',
        'free_zone'    => '免费专区',
    ],
    'live_telecast' => [
        'SDKAppID'    => '1400045393',
        'accountType' => '18177',
    ],
];