<?php
namespace App\Crontab\Item;

use App\HttpController\Api\Hdk;
use App\Tool\Tool;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;


/**
 * 获取商品
 * Class GetTbItem
 * @package App\Crontab
 */
class GetTbItem extends AbstractCronTask
{

    public static function getRule(): string
    {
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        return  'GetTbItem';
    }

    function run(int $taskId, int $workerIndex)
    {
        try {

            $min_id = Tool::getCache('min_id');

            $param['min_id'] = $min_id ? $min_id : 1;
            $param['back']   = 50;

            $di = Di::getInstance();

            $di -> set('hdk', new Hdk());

            $hdk = $di -> get('hdk');

            $di -> delete('hdk');

            $res = $hdk -> getItems($param);

            // 如果成功 把下一页的页码存入缓存
            if ( $res['code'] == 200) Tool::setCache('min_id', $res['data']['min_id']);


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