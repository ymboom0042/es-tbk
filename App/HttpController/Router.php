<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/10
 * Time: 16:18
 */
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize( RouteCollector $routeCollector)
    {
        $routeCollector -> addRoute('GET','/test', '/Main/Test/test');
        $routeCollector -> addRoute('GET','/pay', '/Main/Test/pay');

        // redis测试
        $routeCollector -> addRoute('GET','/redis/test', '/Main/Redis/test');

        // mysql测试
        $routeCollector -> addRoute('GET','/mysql/test', '/Main/Mysql/test');

        // DI 依赖注入
        $routeCollector -> addRoute('GET','/IOC/test', '/Main/IOC/test');




        // api路由
        $routeCollector -> addGroup('/api', function (RouteCollector $routeCollector) {

            // 登录注册 logReg
            $routeCollector -> addGroup('/logReg', function (RouteCollector $routeCollector) {

                // 注册
                $routeCollector -> addRoute('POST', '/register', '/Api/LogReg/register');

                // 登录
                $routeCollector -> addRoute('POST', '/login', '/Api/LogReg/login');

            });


            // 好单库 Hdk
            $routeCollector -> addGroup('/hdk', function (RouteCollector $routeCollector) {

                // 商品列表
                $routeCollector -> addRoute('POST', '/itemList', '/Api/Hdk/itemList');

                // 获取商品标题
                $routeCollector -> addRoute('POST', '/getItemTitle', '/Api/Hdk/getItemTitle');

                // 搜索
                $routeCollector -> addRoute('POST', '/searchItem', '/Api/Hdk/searchItem');

                // 商品详情
                $routeCollector -> addRoute('POST', '/itemDetail', '/Api/Hdk/itemDetail');

            });


            // 工具类 Tool
            $routeCollector -> addGroup('/tool', function (RouteCollector $routeCollector) {

                // 获取淘宝渠道授权链接
                $routeCollector -> addRoute('POST', '/getRelationAuthUrl', '/Api/ToolCon/getRelationAuthUrl');

            });


            // 折淘客 Ztk
            $routeCollector -> addGroup('/ztk', function (RouteCollector $routeCollector) {

                $routeCollector -> addRoute('POST', '/deTkl', '/Api/Ztk/deTkl');

            });


            // 工具类 Tool
            $routeCollector -> addGroup('/back', function (RouteCollector $routeCollector) {

                // 获取淘宝渠道授权链接
                $routeCollector -> addRoute(['GET', 'POST'], '/TbAuthCallBack', '/Api/Back/TbAuthCallBack');

            });

        });
    }
}