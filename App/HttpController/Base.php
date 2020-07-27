<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 17:20
 */
namespace App\HttpController;

use App\Jwt\Jwt;
use App\Model\ErrorLog;
use App\Model\User;
use App\Tool\Tool;
use duncan3dc\Laravel\BladeInstance;
use EasySwoole\Component\Di;
use EasySwoole\Http\Exception\Exception;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Validate\Validate;

use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    /**
     * 自动验证错误信息
     * @var 
     */
    protected static $validateErrorMsg;




    /**
     * 自动验证 入口
     * @param $action
     * @param $param
     * @return mixed
     */
    public static function validate( $action, $param )
    {
        self::getErrorMsg($action, $param);

        return self::$validateErrorMsg;
    }


    /**
     * 自动验证 获取错误信息
     * @param $action
     * @param $param
     */
    private static function getErrorMsg( $action, $param )
    {
        $validate = self::validateRule($action, $param);

        $validate_error = $validate -> getError();

        if ( $validate_error )
        {
            self::$validateErrorMsg = $validate_error -> getErrorRuleMsg();
        }
    }


    /**
     * 获取token
     * @return string
     */
    protected function getToken() : string
    {
        $token = '';

        $authorization = $this -> request() -> getHeader('authorization');

        if (!empty($authorization)) $token = $authorization[0];

        return $token;
    }


    /**
     * 解密token 获取用户信息
     * @param $token
     * @return array
     * @throws Exception
     */
    protected function decTokenGetUser( $token ) : array
    {
        try {

            $return = array(
                'status' => 0,
                'msg' => '无效',
                'user' => '',
            );

            if ( $token )
            {
                $return = Jwt::deToken($token);

                if ( $return['status'] )
                {
                    $user_id = $return['user'];

                    if ( $user_id )
                    {
                        $user = User::create() -> get($user_id);

                        if ( $user )
                        {
                            $return['user'] = $user;
                        }
                    }
                }
            }

            return  $return;

        } catch (\Throwable $exception)
        {
            throw  new Exception($exception);
        }

    }

    /**
     * 自动验证
     * @param string|null $action
     * @param array $param
     * @return Validate
     */
    protected static function validateRule(?string $action, array $param): Validate
    {
        $v = new Validate();

        switch ($action)
        {
            // 注册
            case 'reg':
                {
                    $v->addColumn('phone','手机号')
                        ->required('手机号不能为空')
                        ->length(11,'手机号不正确');


                    $v->addColumn('password','密码')
                        ->required('密码不能为空')
                        ->betweenLen(6,12, '密码6-12位');

//                    $v->addColumn('code','验证码')
//                        ->required('验证码不能为空')
//                        ->length(4,'验证码不正确');

                    $v->addColumn('name','用户名')
                        ->required('用户名不能为空')
                        ->betweenLen(6,12, '用户名长度为6-12位');

                }

                break;

            case 'login':
                {
                    $v->addColumn('phone','手机号')
                        ->required('手机号不能为空')
                        ->length(11,'手机号不正确');


                    $v->addColumn('password','密码')
                        ->required('密码不能为空')
                        ->betweenLen(6,12, '密码6-12位');
                }

            case 'alipay':
                {
                    $v->addColumn('amount','金额')
                        ->required('金额不能为空')
                        ->min(0.1,'支付金额不能小于0.1元');


                    $v->addColumn('subject','描述')
                        ->required('描述不能为空');

                    $v->addColumn('trade_no','订单号')
                        ->required('订单号不能为空');

                }

                break;

        }

        // 验证
        $v -> validate($param);

        return $v;
    }


    /**
     * 控制器级别的异常处理 优先级大于自定义
     * @param \Throwable $e
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    protected  function onException(\Throwable $e ) : void
    {
        $err_log = [
            'msg' => $e -> getMessage(),
            'line' => $e -> getLine(),
            'path' => $e -> getFile(),
            'class' => __CLASS__,
            'method' => __METHOD__,
        ];

        ErrorLog::create() -> data($err_log) -> save();

        $this -> writeJson(500, [], '系统异常');

    }


    protected function actionNotFound(?string $action) : void
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
        $this->response()->write("{$action} not found");
    }


    /**
     * 视图
     * @param $view
     * @param $data
     * @throws \Throwable
     */
    public function render($view, $data = [])
    {
        $path    = Tool::getConf('view.view_path');
        $runtime = Tool::getConf('view.cache_path');

        $di = Di::getInstance();

        $di -> set('render', new BladeInstance($path, $runtime));

        $render = $di -> get('render');

        $di -> delete('render');

        $content = $render -> render($view, $data);
        $this->response()->write($content);

    }

    /**
     * http请求  post
     * @param $url
     * @param $data
     * @return array
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    protected function postHttpCurl($url, $data) : array
    {
        $curl = new HttpClient($url);

        $response  = $curl -> post($data);

        return json_decode($response -> getBody(), true) ?? [];


    }

}