<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/13
 * Time: 14:25
 */

namespace App\HttpController\Api;


use App\HttpController\Base;
use App\Model\OrderData;
use App\Tool\Tool;
use EasySwoole\Http\Exception\Exception;

class Ztk extends Base
{

    /**
     * 解析淘口令
     * @param array $param
     * @return false|string
     * @throws Exception
     */
    public  function deTkl( $param = [] )
    {
       if (empty($param)) $param = $this -> request() -> getRequestParam();

        $tkl = $param['keyword'] ?? '';

        if ( $tkl )
        {
            try {

                $req_url = Tool::getConf('ztk.conf.url');

                $url = str_replace("*", 'open_shangpin_id', $req_url);

                // 编码
                $encode_tkl = urlencode($tkl);

                // 拼接URL
                $url = $url . 'content='.$encode_tkl.'&type=1';

                $res = file_get_contents($url);

                var_dump($res);
                return $res;
//                $res = $this -> getHttpCurl($url);

            } catch ( \Throwable $exception) {

                var_dump($exception -> getMessage());
                throw new Exception('系统错误');

            }
        }
        else
        {
            $this -> writeJson(400, [], '淘口令不存在');
        }
    }


    /**
     * 查询订单
     * @param $query_arr
     * @param $method
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function queryOrder($query_arr, $method)
    {
        $url = str_replace("*", 'open_dingdanchaxun2', Tool::getConf('ztk.conf.url'));

        $param = http_build_query($query_arr);

        // 拼接URL
        $url = $url . $param;

        // 折淘客接口获取淘宝订单查询url
        $tb_url = file_get_contents($url);

        if ( $tb_url )
        {
            $tb_url = json_decode($tb_url, true);

            $req_url = $tb_url['url'] ?? '';

            if ( $req_url )
            {
                // 获取订单信息
                $response = file_get_contents($req_url);

                if ( $response )
                {
                    $result = json_decode($response, true);

                    if ( isset($result['tbk_sc_order_details_get_response']))
                    {
                        if ( isset($result['tbk_sc_order_details_get_response']['data']))
                        {
                            $res = $result['tbk_sc_order_details_get_response']['data'];

                            // 插入数据
                            self::insertOrder($res);

                            // 有下一页继续查询
                            if ( $res['has_next'] )
                            {
                                $query_arr['position_index'] = $res['data']['position_index'];
                                $query_arr['page_no'] = $res['data']['page_no'] + 1;

                                self::queryOrder($query_arr, $method);
                            }

                        }
                    }
                }
            }
        }
    }


    /**
     * 插入订单
     * @param $order_data
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    private static function insertOrder( $order_data )
    {
        if ( $order_data && is_array($order_data) && !empty($order_data) )
        {
            if ( !empty( $order_data['results'] ) )
            {
                $data = $order_data['results']['publisher_order_dto'];

                if ( !empty($data) )
                {
                    $insert = [];

                    foreach ( $data as $value)
                    {
                        $insert = array();

                        // 结算佣金 如果订单没有结算 则等于预估佣金
                        $settle_share_fee = $value['pub_share_pre_fee'];

                        // 订单号  父订单 + 子订单
                        $order_no = $value['trade_parent_id'] .'+'. $value['trade_id'];

                        // 查询订单是否存在
                        $order = OrderData::create() -> where('no', $order_no) -> get();

                        if ( $order )
                        {
                            $status = $value['tk_status'];

                            if ( $value['tk_status'] == 3 )
                            {
                                // 结算佣金 如果订单没有结算 则等于预估佣金
                                $settle_share_fee = $value['pub_share_fee'];

                                // 自己平台还未与用户结算 不管什么时间的订单 都是确认收货
                                if ( date('d') <= 21 )
                                {
                                    $status = 14;
                                }
                                else
                                {
                                    if (  $value['tk_paid_time'] )
                                    {
                                        $date = getdate(strtotime($value['tk_paid_time']));

                                        if ( isset($date['mon']))
                                        {
                                            if (date("d") == $date['mon'])
                                            {
                                                $status = 14;
                                            }
                                        }
                                    }
                                }
                            }

                            OrderData::create()  ->
                            update(
                                [
                                    'status' => $status,
                                    'refund_tag'        => $value['refund_tag'],
                                    'tk_earning_time'   => isset($value['tk_earning_time']) ? $value['tk_earning_time'] : null,
                                    'settle_share_fee'  => $settle_share_fee,
                                    'data' => json_encode($value, JSON_UNESCAPED_UNICODE)
                                ], ['no' => $order_no]
                            );
                        }
                        else
                        {
                            if ( isset($value['relation_id']) )
                            {
                                // 获取用户id
                                $user_id = \App\Model\User::where('relation_id', $value['relation_id']) -> val('id');

                                $insert = array(
                                    'user_id'           => $user_id,
                                    'no'                => $order_no,
                                    'item_id'           => $value['item_id'],
                                    'trade_id'          => $value['trade_id'],
                                    'tk_earning_time' => isset($value['tk_earning_time']) ? $value['tk_earning_time'] : null,
                                    'order_create_at'   => $value['tk_create_time'],
                                    'pay_price'         => isset($value['alipay_total_price']) ? $value['alipay_total_price'] : '未知',
                                    'parent_trade_id'   => $value['trade_parent_id'],
                                    'pub_share_fee'     => $value['pub_share_pre_fee'],
                                    'settle_share_fee'  => $settle_share_fee,
                                    'comm_rate'  => isset($value['total_commission_rate']) ? $value['total_commission_rate'] : 0,
                                    'user_pub_share_fee' => OrderData::getPubShare($value['pub_share_pre_fee']),
                                    'data' => json_encode($value, JSON_UNESCAPED_UNICODE),
                                    'status' => $value['tk_status'],
                                );
                            }
                            else
                            {
                                $insert = array(
                                    'no' => $order_no,
                                    'item_id' => $value['item_id'],
                                    'trade_id' => $value['trade_id'],
                                    'tk_earning_time' => isset($value['tk_earning_time']) ? $value['tk_earning_time'] : null,
                                    'order_create_at'   => $value['tk_create_time'],
                                    'pay_price'         => isset($value['alipay_total_price']) ? $value['alipay_total_price'] : '未知',
                                    'parent_trade_id' => $value['trade_parent_id'],
                                    'settle_share_fee'     => $settle_share_fee,
                                    'pub_share_fee' => $value['pub_share_pre_fee'],
                                    'comm_rate'  => isset($value['total_commission_rate']) ? $value['total_commission_rate'] : 0,
                                    'user_pub_share_fee' => sprintf("%.2f", OrderData::getPubShare($value['pub_share_pre_fee'])),
                                    'data' => json_encode($value, JSON_UNESCAPED_UNICODE),
                                    'status' => $value['tk_status'],
                                );
                            }
                        }
                    }

                    if (!empty($insert)) OrderData::create() -> data($insert, false) -> save();
                }
            }
        }

    }
}