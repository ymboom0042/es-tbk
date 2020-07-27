<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 14:23
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class User extends AbstractModel
{

    // 连接数据库(读写分离使用)
    protected $connectionName = 'write';

    // 数据表
    protected $tableName = 'user';


    // 是否开启自动时间戳
    protected $autoTimeStamp = 'datetime';

//    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';


    /**
     * 生成邀请码
     * @return int|string
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function makeInviteCode() : string
    {
        // 生成三位随机数
        $invite_code = rand(1, 9);

        $time = time();

        // 拼上时间戳后5位
        $invite_code .= substr($time, 6);

        $invite_code .= rand(1, 9);

        $id = self::create()
            -> where('invite_code', $invite_code)
            -> val('id');

        if ( $id ) self::makeInviteCode();

        return $invite_code;

    }
}