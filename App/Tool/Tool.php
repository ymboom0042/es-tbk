<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/1
 * Time: 16:38
 */

namespace App\Tool;


use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Exception\Exception;


/**
 * 工具类
 * Class Tool
 * @package App\Tool
 */
class Tool
{
    public static function relationAuthUrl( $user_code )
    {
        try {

            $auth_url = '';

            $relation_url = self::getConf('tb.relation_auth_url');

            if ( $relation_url )
            {
                $app_key = self::getConf('tb.app_key');
                $app_url = self::getConf('app.app_url');

                $auth_url = str_replace('#', $app_url , $relation_url);
                $auth_url = str_replace('*', $user_code, $auth_url);
                $auth_url = str_replace('@', $app_key, $auth_url);
            }

            return $auth_url;

        } catch (\Throwable $e)
        {
            throw new Exception($e);
        }
    }



    /**
     * 对象与数组的转换
     * @param $array
     * @return array
     */
    public static function objectArray($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = self::objectArray($value);
            }
        }
        return $array;
    }


    /**
     * 获取淘宝类
     * @param bool $is_del 是否需要删除依赖注入
     * @return null
     * @throws \Throwable
     */
    public static function getTopClient( $is_del = false)
    {
        include_once EASYSWOOLE_ROOT . '/App/Tool/tbk/TopSdk.php';

        $di = Di::getInstance();

        $di -> set('topClient', new \TopClient());

        $topClient = $di -> get('topClient');

        $topClient -> appkey    = self::getConf('tb.app_key');
        $topClient -> secretKey = self::getConf('tb.app_secret');

        // 删除淘宝入口类依赖注入
       if ( $is_del ) Di::getInstance() -> delete('topClient');

        return $topClient;
    }


    /**
     * 绑定渠道
     * @param $topClient
     * @param $access_token
     * @return array
     * @throws \Throwable
     */
    public static function getTbkInviteCode(  $access_token, $topClient = '' )
    {
        if (!$topClient) $topClient = Tool::getTopClient( true );

        $di = Di::getInstance();

        $di -> set('invite_code', new \TbkScPublisherInfoSaveRequest());

        $auth = $di -> get('invite_code');

        // 删除依赖注入
        $di -> delete('invite_code');

        $auth->setInviterCode(self::getConf('tb.relation_code'));
        $auth->setInfoType(1);

        $resp = $topClient->execute($auth, $access_token);

        // 转为数组
        $res = Tool::objectArray($resp);

        return $res;
    }


    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @param $ttl
     * @return bool
     */
    public static function setCache( string $key, $value, $ttl = 3600 ) : bool
    {
        return Cache::getInstance() -> set($key, $value, $ttl);
    }


    /**
     * 获取缓存内容
     * @param $key
     * @return mixed|null
     */
    public static function getCache( $key )
    {
        return Cache::getInstance() -> get($key);
    }


    /**
     * 返回查询订单需要的参数
     * @param $start
     * @param $end
     * @param mixed ...$args
     * @return array
     */
    public static function getOrderQueryParam( $start, $end, ...$args) : array
    {
         return $query_arr = array(
            'start_time'        => $start,                        // 开始时间
            'end_time'          => $end,                          // 结束时间
            'page_no'           => $args[0] ?? 1,                 // 页码
            'query_type'        => $args[1] ?? 2,                 // 查询时间类型，1:订单淘客创建时间查询，2:付款时间查询，3:结算时间查询。
             'tk_status'        => $args[2] ?? 12,                // 订单状态12-付款，13-关闭，14-确认收货，3-结算成功;不传，表示所有状态。
             'order_scene'      => $args[3] ?? 1,                 // 场景订单场景类型，1:常规订单，2:渠道订单，3:会员运营订单，默认为1

//            'position_index'    => $args[4] ?? 0,
        );
    }



    /**
     * 获取配置
     * @param $path
     * @return array|mixed|null
     */
    public static function getConf( $path )
    {
        return Config::getInstance() -> getConf($path);
    }
}