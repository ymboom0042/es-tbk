<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 15:42
 */
namespace App\HttpController\Main;

use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;


/**
 * 依赖注入 对IOC容器的获取/注入仅限当前进程有效
 * Class IOC
 * @package App\HttpController\Main
 */
class IOC extends Controller
{
    public function test()
    {
//        // 加载依赖注入
//        $ioc = Di::getInstance();
//
//        // 注入
//        $ioc -> set('mysql', new Mysql());
//
//        // 获取数据
//        $val = $ioc -> get('mysql');
//
//        // 调用方法
//        $val -> test();
//
//        // 清除所有注入
//        $ioc ->clear();
//        $ioc ->delete('mysql');
//
//       $val2 =  $ioc -> get('mysql');
//
//       var_dump($val2);

    }
}