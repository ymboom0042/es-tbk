<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/1
 * Time: 16:05
 */

return [
    'app_key' => '',
    'app_secret' => '',

    // 渠道推广码 申请地址： https://survey.taobao.com/apps/zhiliao/0JpI9eizU
    'relation_code' => '',

    // 渠道授权地址
    'relation_auth_url' => 'https://oauth.taobao.com/authorize?response_type=code&client_id=@&redirect_uri=#/api/back/TbAuthCallBack&state=*&view=wap',

    // 渠道授权回调地址
    'relation_back_url' => '#/api/back/TbAuthCallBack'

];