<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 17:19
 */
namespace App\HttpController\Api;

use App\HttpController\Base;
use App\Jwt\Jwt;
use App\Model\User;
use App\Tool\Tool;
use EasySwoole\ORM\Exception\Exception;

class LogReg extends Base
{

    /**
     * 注册
     * @throws Exception
     * @throws \Throwable
     */
    public function register()
    {
        try{

            // 接受参数
            $param = $this -> request() -> getRequestParam();

            // 自动验证
            $validate = $this -> validate('reg', $param);

            if ( !$validate )
            {
                // 获取用户信息
                $user = User::create() -> get(['phone' => $param['phone']]);

                if ( !$user )
                {
                    $user_id = User::create() -> data([
                        'name'  => $param['name'],
                        'phone' => $param['phone'],
                        'password' => md5($param['password']),
                        'invite_code' => User::makeInviteCode(),

                    ]) -> save();

                    // 获取用户信息
                    $user = User::create() -> get($user_id);

                    // 获取token过期时间
                    $ttl = Tool::getConf('jwt.ttl');

                    // 秘钥
                    $secret_key = Tool::getConf('jwt.secret_key');

                    // 获取token
                    $token = Jwt::getToken($user, $ttl, $secret_key);

                    // 返回信息
                    $data = array(
                        'token' => $token,
                        'expire_at' => date("Y-m-d H:i:s", time() + $ttl),
                    );

                    $this -> writeJson(200, $data, '成功');
                }
                else
                {
                    $this -> writeJson(400, [], '账号已存在');

                }
            }
            else
            {
                $this -> writeJson(203, [], $validate);
            }

        } catch (\Throwable $e)
        {
            var_dump($e -> getMessage());
            throw new Exception($e);
        }

    }


    /**
     * 登录
     * @throws Exception
     */
    public function login()
    {
        try {

            // 接受参数
            $param = $this -> request() -> getRequestParam();

            // 自动验证
            $validate = $this -> validate('login', $param);

            if ( !$validate )
            {
                // 获取用户信息
                $user = User::create() -> get(['phone' => $param['phone']]);

                if ( $user )
                {
                    if ( $user -> password == md5($param['password']))
                    {
                        // 获取token过期时间
                        $ttl = Tool::getConf('jwt.ttl');

                        // 秘钥
                        $secret_key = Tool::getConf('jwt.secret_key');

                        // 获取token
                        $token = Jwt::getToken($user, $ttl, $secret_key);

                        // 返回信息
                        $data = array(
                            'token' => $token,
                            'type' => 'Bearer',
                            'expire_at' => date("Y-m-d H:i:s", time() + $ttl),
                        );

                        $this -> writeJson(200, $data, '成功');
                    }
                    else
                    {
                        $this -> writeJson(400, [], '账号或密码错误');
                    }

                }
                else
                {
                    $this -> writeJson(400, [], '账号不存在,请先注册');
                }
            }

        } catch (\Throwable $e)
        {
            throw new Exception($e);
        }
    }
}