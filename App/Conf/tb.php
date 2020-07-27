<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/1
 * Time: 16:05
 */

return [
    'app_key' => '30425935',
    'app_secret' => 'a0bfd26d50d526abd6b4cb9890acc723',

    // 渠道推广码 申请地址： https://survey.taobao.com/apps/zhiliao/0JpI9eizU
    'relation_code' => 'ZJHVZ3',

    // 渠道授权地址
    'relation_auth_url' => 'https://oauth.taobao.com/authorize?response_type=code&client_id=@&redirect_uri=#/api/back/TbAuthCallBack&state=*&view=wap',

    // 渠道授权回调地址
    'relation_back_url' => '#/api/back/TbAuthCallBack'

];