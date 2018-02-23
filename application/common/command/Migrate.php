<?php

namespace app\common\command;

use app\v1\model\User;
use Hashids\Hashids;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Migrate extends Command
{

    //命令描述
    protected function configure()
    {
        $this->setName('migrate')->setDescription('cancel the goods activity');
    }

    //所要执行的命令
    protected function execute(Input $input, Output $output)
    {
        $db_new = 'mysql://root:1nN7u=hY+[f6[Tji@47.92.5.249:3306/ZHSQ#utf8mb4';
        $db_old = 'mysql://root:123456@192.168.1.150:3306/zhsq_test#utf8';
        exit;
        try {
            $user     = Db::connect($db_old)->table('yun_users')->select();
            $count    = count($user);
            $start_id = 1001;
            foreach ($user as $key => $val) {
                if (!$val['mobile'] || strlen($val['mobile']) != 11) {
                    continue;
                }
                if (Db::connect($db_new)->name('user')->where('mobile', $val['mobile'])->find()) {
                    continue;
                }
                $user_id = $key + $start_id;
                $hashids = new Hashids('', 8, '0123456789ABCDEF');
                $uuid    = $hashids->encode($user_id);
                Db::connect($db_new)->name('user')->insert([
                    'id'            => $user_id,
                    'uuid'          => $uuid,
                    'shop_id'       => 0,
                    'community_id'  => 3,
                    'mobile'        => $val['mobile'],
                    'nickname'      => $val['nickname'],
                    'idcard'        => $val['card'],
                    'task_time'     => $val['money'],
                    'register_time' => $val['reg_time'],
                    'face'          => null,
                    'sex'           => null,
                    'update_time'   => $val['last_time'] ? $val['last_time'] : $val['reg_time'],
                    'auth_type'     => 0,
                    'status'        => 1,
                    'password'      => User::passwordEncrypt($val['password'], true),
                ]);
                $output->error($key . '/' . $count);
            }

        } catch (\PDOException $e) {
            $output->error($e->getMessage());
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }
        $output->writeln("数据迁移完成");
    }
}