<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/24
 * Time: 15:12
 */
namespace App\Service;

use App\HttpController\Base;
use App\Tool\Tool;
use EasySwoole\Component\Di;
use EasySwoole\Component\Singleton;
use EasySwoole\Pay\AliPay\Config;
use EasySwoole\Pay\AliPay\GateWay;
use EasySwoole\Pay\AliPay\RequestBean\App;
use EasySwoole\Pay\AliPay\RequestBean\Web;
use EasySwoole\Pay\Pay;


/**
 * 支付
 * Class PayService
 * @package App\Service
 */
class alipayService
{
    use Singleton;

    /**
     * 网页支付
     * @param array $params
     * @return array
     * @throws \EasySwoole\Pay\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function webPay( array $params ) : array
    {
        $validate = Base::validate('alipay', $params);

        if ( !$validate )
        {
            // 支付宝配置
            $alipayConf = $this -> getAlipayConf();

            // 订单数据
            $order = $this -> getWebPay();
            $order -> setSubject($params['subject']);
            $order -> setOutTradeNo($params['trade_no']);
            $order -> setTotalAmount($params['amount']);

            $pay = $this -> getPay();

            $res = $pay -> aliPay($alipayConf) -> web($order) -> toArray();

            // 创建支付页面
            $html = $this -> buildPayHtml(GateWay::NORMAL, $res);

            $pay_html = EASYSWOOLE_ROOT . '/App/View/pay_html/web/' . $params['trade_no'] . '.blade.php';

            file_put_contents($pay_html,$html);

            return [true, $params['trade_no']];
        }
        else
        {
            return [false, $validate];
        }
    }


    /**
     * app支付
     * @param array $params
     * @return array
     * @throws \EasySwoole\Pay\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function appPay( array $params) : array
    {
        $validate = Base::validate('alipay', $params);

        if ( !$validate )
        {
            // 支付宝配置
            $alipayConf = $this -> getAlipayConf();

            // 订单数据
            $order = $this -> getAppPay();
            
            $order -> setSubject($params['subject']);
            $order -> setOutTradeNo($params['trade_no']);
            $order -> setTotalAmount($params['amount']);

            $pay = $this -> getPay();

            $res = $pay -> aliPay($alipayConf) -> app($order) -> toArray();

            var_dump($res);

            return [true, $params['trade_no']];
        }
        else
        {
            return [false, $validate];
        }
    }



    /**
     * 获取支付类
     * @return Pay
     * @throws \Throwable
     */
    private function getPay() : Pay
    {
        $payClass = $alipayConf = Di::getInstance() -> get('payClass');

        if ( !$payClass )
        {
            $di = Di::getInstance();

            $di -> set('payClass', new Pay());

            $payClass = $di -> get('payClass');
        }

        return $payClass;
    }

    


    /**
     * 获取支付宝配置信息
     * @return Config
     * @throws \Throwable
     */
    private function getAlipayConf() : Config
    {
        $alipayConf = Di::getInstance() -> get('alipayConf');

        if ( !$alipayConf )
        {
            $di = Di::getInstance();
            $di -> set('alipayConf', new Config());

            $alipayConf = $di -> get('alipayConf');

            // 支付宝配置信息
            $alipay = Tool::getConf('pay.alipay');

            $alipayConf -> setAppId($alipay['app_id']);
            $alipayConf -> setPublicKey($alipay['pub_key']);
            $alipayConf -> setPrivateKey($alipay['pri_key']);
        }

        return $alipayConf;
    }


    /**
     * 网页支付
     * @return Web
     * @throws \Throwable
     */
    private function getWebPay() : Web
    {
        $webPay = Di::getInstance() -> get('webPay');

        if ( !$webPay )
        {
            $di = Di::getInstance();
            $di -> set('webPay', new Web());

            $webPay = $di -> get('webPay');

        }

        return $webPay;
    }


    /**
     * app支付
     * @return App
     * @throws \Throwable
     */
    private function getApppay() : App
    {
        $appPay = Di::getInstance() -> get('appPay');

        if ( !$appPay )
        {
            $di = Di::getInstance();
            $di -> set('appPay', new App());

            $appPay = $di -> get('appPay');

        }

        return $appPay;
    }

    /**
     * 构建网页支付页面
     * @param $endpoint
     * @param $payload
     * @return string
     */
    private function buildPayHtml($endpoint, $payload)
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$endpoint."' method='POST'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }

}