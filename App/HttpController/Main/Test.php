<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/10
 * Time: 16:27
 */
namespace App\HttpController\Main;

use App\HttpController\Base;
use App\Service\alipayService;

class Test extends Base
{

    public function test()
    {
        try {

            $order = array(
                'subject' => '测试',
                'amount' => 0.1,
                'trade_no' => time() . rand(11111, 99999),
            );

            [$status, $data] = alipayService::getInstance() -> scanPay($order);

            var_dump($data);

        } catch (\Throwable $e)
        {
            var_dump($e -> getMessage());
        }

    }
}