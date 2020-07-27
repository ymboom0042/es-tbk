<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 14:23
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class Sys extends AbstractModel
{

    // 连接数据库(读写分离使用)
    protected $connectionName = 'write';

    // 数据表
    protected $tableName = 'sys';

    // 是否开启自动时间戳
    protected $autoTimeStamp = 'datetime';

//    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

}