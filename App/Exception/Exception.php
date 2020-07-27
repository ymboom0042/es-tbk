<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/12
 * Time: 9:47
 */
namespace App\Exception;


use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

/**
 * 自定义异常 在EasySwooleEvent@initialize中注册
 * 当前使用控制器级别 HttpController/Base中
 * Class Exception
 * @package App\Exception
 */
class Exception
{
    public static function handle( \Throwable $exception, Request $request, Response $response )
    {
//        var_dump($exception -> getMessage());
//        var_dump($exception->getTraceAsString());
    }
}