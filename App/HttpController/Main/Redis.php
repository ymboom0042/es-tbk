<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 10:55
 */
namespace App\HttpController\Main;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Pool\Manager;

class Redis extends Controller
{
    public function test()
    {
        // getObj
        $redis = Manager::getInstance() -> get('redis') -> getObj();

//        if(!$res=$redis->get("name")){
//            $redis->set("name","zq");
//            $redis->expire("name",400);
//        }

        $res = $redis->get("name");

        // 回收
        Manager::getInstance() -> get('redis') -> recycleObj($redis);

        return $this->writeJson(200,$res);

    }
}