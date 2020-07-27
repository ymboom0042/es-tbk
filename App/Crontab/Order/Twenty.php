<?php
namespace App\Crontab\Order;

use App\HttpController\Api\Ztk;
use App\Tool\Tool;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;


/**
 * 每5分钟查询前20分钟的订单
 * Class GetTbItem
 * @package App\Crontab
 */
class Twenty extends AbstractCronTask
{

    public static function getRule(): string
    {
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        return  'Twenty';
    }

    function run(int $taskId, int $workerIndex)
    {
        try {

            // 20分钟秒数
            $tewnty_second = 20 * 60;

            // 订单付款开始时间
            $start_t = date("Y-m-d H:i:s", time() - $tewnty_second);

            // 订单付款结束时间
            $end_t = date("Y-m-d H:i:s", time());

            $param = Tool::getOrderQueryParam($start_t, $end_t);

            Ztk::queryOrder($param, 'twenty');

        } catch (\Throwable $e)
        {
            var_dump($e -> getMessage());
        }

    }


    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}