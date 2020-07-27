<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/27
 * Time: 18:03
 */

namespace App\Service;


use App\Tool\Tool;
use EasySwoole\Component\Di;
use EasySwoole\Component\Singleton;
use EasySwoole\Pay\Pay;
use EasySwoole\Pay\WeChat\Config;
use EasySwoole\Pay\WeChat\RequestBean\OfficialAccount;

class wechatService
{
    use Singleton;


    /**
     * 公众号支付
     * @param array $params
     * @throws \Throwable
     */
    public function OfficialPay(array $params)
    {
        $config = $this->getWechatPayConf();

        $official = $this -> getOfficialPayCls();

        $official -> setOpenid($params['open_id']);
        $official -> setOutTradeNo($params['trade_no']);
        $official -> setBody($params['body']);
        $official -> setTotalFee($params['total_fee']);
        $official -> setSpbillCreateIp($params['spbill_create_ip']);

        $pay = $this->getPay();

        $params = $pay->weChat($config)->officialAccount($official);


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
    private function getWechatPayConf() : Config
    {
        $wechatConf = Di::getInstance() -> get('wechatConf');

        if ( !$wechatConf )
        {
            $di = Di::getInstance();
            $di -> set('wechatConf', new Config());

            $wechatConf = $di -> get('wechatConf');

            // 支付宝配置信息
            $wechat = Tool::getConf('pay.wechat');

            $wechatConf -> setAppId($wechat['app_id']);
            $wechatConf -> setMchId($wechat['mch_id']);
            $wechatConf -> setKey($wechat['key']);
            $wechatConf -> setNotiftUrl($wechat['notify_url']);
            $wechatConf -> setApiClientCert($wechat['client_cert']);
            $wechatConf -> setApiClientKey($wechat['client_key']);
        }

        return $wechatConf;
    }


    /**
     * 公众号支付实例
     * @return OfficialAccount
     * @throws \Throwable
     */
    private function getOfficialPayCls() : OfficialAccount
    {
        $officialPay = Di::getInstance() -> get('officialPay');

        if ( !$officialPay )
        {
            $di = Di::getInstance();
            $di -> set('officialPay', new OfficialAccount());

            $officialPay = $di -> get('officialPay');

        }

        return $officialPay;
    }
}