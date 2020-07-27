<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/30
 * Time: 16:06
 */

namespace App\HttpController\Api;


use App\HttpController\Base;
use App\Tool\Tool;


class Wy extends Base
{

    /**
     * 解密淘口令
     * @param $tkl
     * @return array|bool|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function decTkl( string $tkl ) : array
    {
        $key = Tool::getConf('hdk.wy.key');
        $url = Tool::getConf('hdk.wy.url');

        $url =  $url . 'dec?vekey='.$key . '&para=' . $tkl;

        $res = self::postHttpCurl($url, []);

        if (!isset($res['error']) )
        {
            return $res;
        }
        else
        {
            if ( $res['error'] == 0 )
            {
                return $res;
            }
        }

        return [];


    }
}