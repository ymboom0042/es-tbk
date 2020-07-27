<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/1
 * Time: 16:48
 */

namespace App\HttpController\Api;


use App\HttpController\Base;
use App\Model\UserRelation;
use App\Tool\Tool;
use EasySwoole\Http\Exception\Exception;


/**
 * 回调类
 * Class Back
 * @package App\HttpController\Api
 */
class Back extends Base
{

    /**
     * 淘宝渠道授权回调
     * @throws Exception
     */
    public function TbAuthCallBack()
    {
        try {

            $param = $this -> request() -> getRequestParam();

//            $param = '{"state":"87515423","ttid":"2014_0_29234913@baichuan_iphone_4.0.1.0","code":"EN73lX95cw79pYawlP0HvTm22853562"}';

            if ( !empty($param))
            {
                if ( isset($param['code']) && isset($param['state']))
                {
                    $code        = $param['code'];
                    $invite_code = $param['state'];

                    // 淘宝sdk
                    $topClient = Tool::getTopClient( true );

                    // 官方SDK报错 不知道原因
//                    $di = Di::getInstance();
//
//                    $di -> set('relation_auth', new \TopAuthTokenCreateRequest());
//
//                    $auth = $di -> get('relation_auth');
//
//                    $auth->setCode($code);
//
//                    $resp = $topClient->execute($auth);
//
//                    var_dump($resp);

                     $url = 'https://oauth.taobao.com/token';

                     // 回调地址
                     $app_url = Tool::getConf('app.app_url');
                     $redirect_uri = str_replace('#', $app_url , Tool::getConf('tb.relation_back_url'));

                    $post_fields = array(
                        'grant_type'    =>'authorization_code',
                        'client_id'     =>Tool::getConf('tb.app_key'),
                        'client_secret' =>Tool::getConf('tb.app_secret'),
                        'code'          => $code,
                        'redirect_uri'  => $redirect_uri
                    );

                    $result = self::postHttpCurl($url, $post_fields);

                    // 转为数组
//                    $res = Tool::objectArray($resp);

                    if ( !empty($result))
                    {
                        $access_token = $result['access_token'] ?? '';

                        if ( $access_token )
                        {
                            $relation = Tool::getTbkInviteCode($access_token, $topClient);

                            if ( isset($relation['data']) )
                            {
                                $data = $relation['data'];

                                if ( isset($data['relation_id']))
                                {
                                    $relation_id = $data['relation_id'];

                                    $user= \App\Model\User::create() -> get(['invite_code' => $invite_code]);

                                    if ( $user )
                                    {
                                        if ( $relation_id )
                                        {
                                            // 用户已经授权渠道 更新授权
                                            if ( $user -> relation_id )
                                            {
                                                UserRelation::create() -> update([
                                                    'relation_id'   => $relation_id,
                                                    'accesstoken'   => $access_token,
                                                    'taobao_nick'   => isset($result['taobao_user_nick']) ? urldecode($result['taobao_user_nick']) : '',
                                                    'taobao_id'     => isset($result['taobao_user_id']) ? $result['taobao_user_id'] : '',
                                                ], ['id' => $user -> id]);
                                            }
                                            else
                                            {
                                                UserRelation::create() -> data([
                                                    'user_id'       => $user -> id,
                                                    'relation_id'   => $relation_id,
                                                    'accesstoken'   => $access_token,
                                                    'taobao_nick'   => isset($result['taobao_user_nick']) ? urldecode($result['taobao_user_nick']) : '',
                                                    'taobao_id'     => isset($result['taobao_user_id']) ? $result['taobao_user_id'] : '',
                                                ], false) -> save();
                                            }


                                            \App\Model\User::create() -> update([
                                                'relation_id' => $relation_id,
                                            ], ['id' => $user -> id]);

                                            $msg = '授权成功';

                                            $this -> render('auth_back/suc', ['msg' => $msg]);
                                        }
                                        else
                                        {
                                            $msg = '授权失败，请稍后尝试:2000';
                                        }
                                    }
                                    else
                                    {
                                        $msg = '授权失败，请稍后尝试:2001';
                                    }

                                }
                                else
                                {
                                    $msg = '授权失败，请稍后尝试:2002';
                                }
                            }
                            else
                            {
                                if ( isset($relation['sub_msg']))
                                {
                                    $msg = $relation['sub_msg'];
                                }
                                else
                                {
                                    $msg = '授权失败，请稍后尝试:2003';
                                }
                            }
                        }
                        else
                        {
                            $msg = '授权失败，请稍后尝试:2004';
                        }
                    }
                    else
                    {
                        $msg = '授权失败，请稍后尝试:2005';
                    }
                }
                else
                {
                    $msg = '授权失败，请稍后尝试:2006';
                }
            }
            else
            {
                $msg = '授权失败，请稍后尝试:2007';
            }

            $this -> render('auth_back/error', ['msg' => $msg]);

        } catch (\Throwable $exception)
        {
            var_dump($exception -> getMessage());
            throw new Exception($exception);
        }
    }
}