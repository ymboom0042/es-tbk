<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 14:23
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class Item extends AbstractModel
{

    // 连接数据库(读写分离使用)
    protected $connectionName = 'write';

    // 数据表
    protected $tableName = 'item';


    // 是否开启自动时间戳
    protected $autoTimeStamp = 'datetime';

//    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';




    /**
     * 返回商品视频地址
     * @param $video_id
     * @param $video_url
     * @return mixed|null
     */
    public static function getGoodVideo( $video_id, $video_url )
    {
        $url = '';

        if ( $video_id )
        {
            $url = str_replace('*', $video_id, $video_url);
        }

        return $url;
    }
}