<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/1
 * Time: 16:36
 */

namespace App\HttpController\Api;


use App\HttpController\Base;
use App\Tool\Tool;
use EasySwoole\Http\Exception\Exception;

class ToolCon extends Base
{

    /**
     * 获取授权链接
     * @throws Exception
     */
    public function getRelationAuthUrl()
    {
        try {

            // 获取token
            $token = $this->getToken();

            if ( $token )
            {
                $user_info  = $this -> decTokenGetUser($token);

                if ( $user_info['status'] )
                {
                    // 用户数据
                    $user = $user_info['user'];

                    // 获取授权链接
                    $url = Tool::relationAuthUrl($user -> invite_code);

                    $this -> writeJson(200, ['auth_url' => $url], '成功');
                }
                else
                {
                    $this -> writeJson(400,[], $user_info['msg']);
                }
            }
            else
            {
                $this -> writeJson(400,[], '登录无效,请重新登录');
            }


        } catch (\Throwable $exception)
        {
            throw new Exception($exception);
        }
    }
}