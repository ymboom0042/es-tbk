<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 16:55
 */
namespace App\Jwt;
use EasySwoole\Http\Exception\Exception;
use EasySwoole\Jwt\Jwt as easyJwt;

class Jwt
{
    /**
     * 生成token
     * @param $user
     * @param int $ttl 过期时间  秒
     * @param string $secret_key 秘钥
     * @return false|string
     */
    public static function getToken( $user, $ttl, $secret_key ) : string
    {
        $jwtObj = easyJwt::getInstance() -> setSecretKey($secret_key) -> publish();

        $jwtObj -> setAud($user);
        $jwtObj -> setExp(time() + $ttl);
        $jwtObj -> setJti(md5(time() + $ttl));

        $token = $jwtObj->__toString();

        return $token;
    }


    /**
     *  解密token
     * @param $token
     * @return array
     * @throws Exception
     */
    public static function deToken( $token ) : array
    {
        try{

            $return = array(
                'status' => 0,
                'msg' => '无效',
                'user' => '',
            );

            $jwtDeObj = easyJwt::getInstance() ->setSecretKey('ymboom')-> decode($token);

            $status = $jwtDeObj -> getStatus();

            switch ( $status )
            {
                case 1:

                    $jwtDeObj->getAlg();
                    $jwtDeObj->getAud();
                    $jwtDeObj->getData();
                    $jwtDeObj->getExp();
                    $jwtDeObj->getIat();
                    $jwtDeObj->getIss();
                    $jwtDeObj->getNbf();
                    $jwtDeObj->getJti();
                    $jwtDeObj->getSub();
                    $jwtDeObj->getSignature();
                    $jwtDeObj->getProperty('alg');

                    $return = array(
                        'status' => 1,
                        'msg' => '验证通过',
                        'user' => $jwtDeObj->getAud()['id'],
                    );

                    break;

                case -1:

                    $return['msg'] = '非法请求';

                    break;

                case -2:
                    $return['msg'] = '登录失效,请重新登录';

                    break;
            }


            return $return;
        } catch ( \Throwable $exception)
        {
            throw new Exception($exception);
        }
    }
}