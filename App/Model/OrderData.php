<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 14:23
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class OrderData extends AbstractModel
{

    // 连接数据库(读写分离使用)
    protected $connectionName = 'write';

    // 数据表
    protected $tableName = 'order_data';


    // 是否开启自动时间戳
    protected $autoTimeStamp = 'datetime';

//    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';


    /**
     * 获取自购商品返佣
     * @param $item_pub_share
     * @return string
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function getPubShare( $item_pub_share )
    {
        $sys = Sys::create() -> get();

        // 自购  = （自购比例 + 平台补贴）* 商品返佣金额 / 100
        return sprintf("%.2f", ($sys -> member_self_buy + $sys -> platform_subsidy) * $item_pub_share / 100);
    }

}