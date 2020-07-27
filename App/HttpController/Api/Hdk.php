<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/12
 * Time: 11:33
 */

namespace App\HttpController\Api;


use App\HttpController\Base;
use App\Model\Item;
use App\Tool\Tool;
use EasySwoole\Component\Di;
use EasySwoole\Http\Exception\Exception;


/**
 * 好单库类
 * 接口地址 https://www.haodanku.com/api/detail
 * Class hdk
 * @package App\HttpController\Api
 */
class Hdk extends Base
{
//
//    function onException(\Throwable $throwable): void
//    {
//        var_dump($throwable -> getTrace());
//    }


    /**
     * 商品列表
     * @throws Exception
     */
    public function itemList()
    {
        try {

            $param = $this -> request() -> getRequestParam();

            $res = $this -> getItems( $param );

            $this -> writeJson($res['code'], $res['data'], $res['msg']);

        } catch (\Throwable $e)
        {
            var_dump($e -> getMessage());

            throw new Exception($e);
        }


    }


    /**
     * 获取商品商品列表
     * @param array $param
     * @return array
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getItems( array $param ) : array
    {
        $res['code'] = 400;
        $res['msg']  = '暂无商品';
        $res['data'] = [];

        $url = $this -> splicingReqUrl('item_list', $param);

        $result = $this -> postHttpCurl($url, []);

        if ( !empty($result) )
        {
            if ( isset($result['code']) && isset($result['msg']) && isset($result['data']))
            {
                if ( $result['code'] === 1 && $result['msg'] == 'SUCCESS' && !empty($result['data']))
                {
                    $res['code'] =  200;
                    $res['msg']  = '成功';

                    // 分页
                    $res['data']['min_id']  = $result['min_id'] ?? 2;

                    $datas = $result['data'];

                    foreach ($datas as $data)
                    {
                        $re = $this -> existGood($data, true);
                        $res['data']['item'][] = $re;
                    }
                }
            }
        }


        return $res;
    }


    /**
     * 协程版本商品列表
     * @throws \Exception
     */
//    public function goItemList()
//    {
//        $param = $this -> request() -> getRequestParam();
//
//        $res['code'] = 400;
//        $res['msg']  = '暂无商品';
//        $res['data'] = [];
//
//        try{
//
//           var_dump($res['mmm']);
//
//            $channel = new \Swoole\Coroutine\Channel(1);
//
//            go(function () use ($param, $channel, $res)  {
//
//                $url = $this -> splicingReqUrl('item_list', $param);
//
//                $result = $this -> postHttpCurl($url, []);
//
//                if ( !empty($result) )
//                {
//                    if ( isset($result['code']) && isset($result['msg']) && isset($result['data']))
//                    {
//                        if ( $result['code'] === 1 && $result['msg'] == 'SUCCESS' && !empty($result['data']))
//                        {
//                            $datas = $result['data'];
//
//                            foreach ($datas as $data)
//                            {
//                                $re = $this -> existGood($data);
//                                $res['data'][] = $re;
//                            }
//
//                            $res['code'] =  200;
//                            $res['msg']  = '成功';
//                        }
//                    }
//                }
//
//                $channel -> push($res);
//            });
//
//            $res = $channel -> pop();
//
//            $this -> writeJson($res['code'], $res['data'], $res['msg']);
//
//        } catch (Exception $e) {
//
//            throw new \Exception('系统错误');
//
//            // 记录日志
//        }
//    }


    /**
     * 获取商品标题
     * @throws Exception
     */
    public function getItemTitle()
    {
        try{



            $res['code'] = 400;
            $res['msg']  = '暂无商品';
            $res['data'] = [];

            $param = $this -> request() -> getRequestParam();

            $z = new Ztk();

            $z -> deTkl($param);


            if ( isset($param['keyword']))
            {
                // 是否为数字 可能为商品id
                if ( !is_numeric($param['keyword']))
                {
                    // 依赖注入调用其他类中的方法
                    $di = Di::getInstance();
                    $di ->set('wy', new Wy());
                    $wy = $di -> get('wy');

                    $result = $wy -> decTkl($param['keyword'], 'title');

                    // 清除注入
                    $di -> delete('wy');

                    $res['code'] = 200;

                    if ( !empty($result) )
                    {
                        $res['msg'] = '查询成功';
                        $res['code'] = 200;
                        $res['data']['title']   = $result['title'];
                        $res['data']['item_id'] = $result['num_iid'];

                    }
                    else
                    {
                        $res['data']['title'] = $param['keyword'];
                        $res['data']['item_id'] = '';
                    }
                }
                else
                {
                    $res['msg'] = '查询成功';
                    $res['code'] = 200;
                    $res['data']['title']   = $param['keyword'];
                    $res['data']['item_id'] = $param['keyword'];
                }

            }else {

              $res['msg']  = 'keyword不能为空';

            }

            $this -> writeJson($res['code'], $res['data'], $res['msg']);

        } catch (\Throwable $e){

            throw new Exception($e);
        }
    }


    /**
     * 超级搜索 keyword为getItemTitle中的item_id
     * @param array $param
     * @throws Exception
     */
    public function searchItem( $param = [] )
    {
        try{

            $res['code'] = 400;
            $res['msg']  = '暂无商品';
            $res['data'] = [];

            if (empty($param)) $param = $this -> request() -> getRequestParam();

            if ( isset($param['keyword']))
            {
                $url = $this -> splicingReqUrl('super_search', $param);

                $result = $this -> postHttpCurl($url, []);

                if ( !empty($result) )
                {
                    if ( isset($result['code']) && isset($result['msg']) && isset($result['data']))
                    {
                        if ( $result['code'] === 1 && $result['msg'] == 'SUCCESS' && !empty($result['data']))
                        {
                            $res['code'] =  200;
                            $res['msg']  = '成功';

                            $datas = $result['data'];

                            foreach ($datas as $data)
                            {
                                $re = $this -> existGood($data, true);
                                $res['data'][] = $re;

                            }
                        }
                    }
                }

            }
            else
            {
                $res['msg']  = 'keyword不能为空';
            }

            $this -> writeJson($res['code'], $res['data'], $res['msg']);

        } catch (\Throwable $e){

            throw new Exception($e);
        }
    }


    /**
     * 商品详情
     * @throws Exception
     */
    public function itemDetail()
    {
        try{

            $res['code'] = 400;
            $res['msg']  = '暂无商品';
            $res['data'] = [];

            $param = $this -> request() -> getRequestParam();

            $item_id = $param['item_id'] ?? '';

            if ( $item_id )
            {
                // 查询是否有商品
                $item = Item::create() -> get(['item_id' => $item_id]);

                if ( $item )
                {
//                    var_dump($item -> title);

                    $data = json_decode($item -> datas, true);

                    $token = $this -> getToken();

                    $user_info = $this -> decTokenGetUser($token);

                    $high = array(
                        'coupon_url'   => '',
                        'item_url'     => '',
                        'tkl_pwd'      => '',
                    );

                    $is_bind = false;

                    if ( $user_info['status'] )
                    {
                        $user = $user_info['user'];

                        $high = $this -> getHighComm($item -> item_id, $item -> title, $user -> relation_id);

                        if ( $user -> relation_id) $is_bind = true;
                    }

                    // 商品信息
                    $res['data']['item'] = array(
                        'price'             => $data['itemprice'],
                        'title'             => $data['itemtitle'],
                        'desc'              => $data['itemdesc'],
                        'volume'            => $data['itemsale'],
                        'pic'               => $data['itempic'] . '_310x310.jpg',
                        'coupon_price'      => $data['itemendprice'],
                        'coupon_money'      => $data['couponmoney'],
                        'coupon_url'        => $high['coupon_url'],
                        'tkl_pwd'           => $high['tkl_pwd'],
                        'is_bind'           => $is_bind,
                        'coupon_start_time' => $data['couponstarttime'] ? date("Y.m.d", $data['couponstarttime']) : '',
                        'coupon_end_time'   => $data['couponendtime'] ? date("Y.m.d", $data['couponendtime']) : '',
                        'taobao_image'      => isset($data['taobao_image']) ? explode(',', $data['taobao_image']) : [],
                        'platform'        => 'tb',
                        'detail_url'        => 'https://detail.tmall.com/item.htm?id='. $item_id ,
                    );

                    // 店铺信息
                    $res['data']['shop'] = array(
                        'shop_type'         => $data['shoptype'],
                        'seller_name'       => isset($data['seller_name']) ? $data['seller_name']: '',
                        'seller_nick'       => isset($data['sellernick']) ? $data['sellernick']: '',
                        'shop_name'         => isset($data['shopname']) ? $data['shopname'] : '',
                        'shop_score'        => isset($data['shop_score']) ? $data['shop_score'] : [],
                    );
                }
                else
                {
                    // 没有就搜索获取
                    $this -> searchItem($param);

                    $res['code'] = 200;
                    $res['msg']  = '网络异常,请刷新重试';
                }
            }

            $this -> writeJson($res['code'], $res['data'], $res['msg']);


        } catch (\Throwable $e) {

            var_dump($e -> getMessage());
            throw new Exception($e);
        }
    }


    public function get()
    {
        
    }

    /**
     * 获取高佣优惠券链接
     * @param string $item_id
     * @param string $title
     * @param int $relation_id
     * @return array
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    private function getHighComm( string $item_id, string $title, int $relation_id ) : array
    {
        $return = array(
            'coupon_url'         => '',
            'item_url'           => '',
            'tkl_pwd'            => '',
        );

//        $relation_id = '';

        if ( $relation_id )
        {
            $param['itemid']         = $item_id;
            $param['relation_id']    = $relation_id;
            $param['get_taoword']    = 1;
            $param['pid']            = Tool::getConf('hdk.conf.pid');
            $param['tb_name']        = Tool::getConf('hdk.conf.tb_name');
            $param['title']          = $title;

            $url = $this -> splicingReqUrl('high_comm', $param, 1);

            $res = $this -> postHttpCurl($url, $param);

            if ( !empty($res) )
            {
                if ( $res['code'] == 1 && $res['msg'] == 'SUCCESS' )
                {
                    $data = $res['data'];

                    $return = array(
                        'coupon_url'   => $data['coupon_click_url'],
                        'rate'               => $data['max_commission_rate'],
                        'item_url'           => $data['coupon_click_url'],
                        'tkl_pwd'            => $data['taoword'],
                        'coupon_money'       => $data['couponmoney'],
                    );


                }
            }
        }

        return $return;
    }


    
    
    

    /**
     * 拼接请求地址
     * @param string $route method
     * @param array $param 参数
     * @param int $post
     * @return mixed|string
     */
    private function splicingReqUrl($route, $param, $post = 0) :string
    {
        $method = Tool::getConf('hdk.url.' . $route);

        $key = Tool::getConf('hdk.conf.key');
        $url = Tool::getConf('hdk.conf.url') .'/*'. '/apikey/' . $key;

        // 请求地址
        $url = str_replace('*', $method, $url);

        if ( !$post )
        {
            if ( is_array($param) && !empty($param))
            {
                foreach ($param as $item => $value)
                {
                    $url .= '/' . $item . '/' . $value;
                }
            }
        }


        return $url;
    }

    /**
     * 商品是否存在  插入/更新商品数据
     * @param $data
     * @param  bool $is_unset 是否删除一些不需要的字段
     * @return array
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function existGood( array $data, bool $is_unset = false) : array
    {
        $array = array();

        $item_id = isset($data['itemid']) ? $data['itemid'] : 0;

        if ( $item_id )
        {

            $item = Item::create() -> get(['item_id' => $item_id]);

            $tk_money = isset($data['tkmoney']) ? $data['tkmoney'] : sprintf("%.2f", $data['itemendprice'] * $data['tkrates'] / 100);

            $array = array(
                'item_id'         => $data['itemid'],
                'price'           => $data['itemprice'],
                'cid'             => isset($data['fqcat']) ? $data['fqcat'] : 0,
                'title'           => $data['itemtitle'],
                'volume'          => $data['itemsale'],
                't_hour_sale'     => isset($data['itemsale2']) ? $data['itemsale2'] : 0,
                'today_sale'      => isset($data['todaysale']) ? $data['todaysale'] : 0,
                'pic'             => $data['itempic'],
                'taobao_image'    => isset($data['taobao_image']) ? $data['taobao_image'] : null,
                'coupon_price'    => $data['itemendprice'],
                'coupon_url'      => $data['couponurl'],
                'coupon_money'    => $data['couponmoney'],
                'is_brand'        => isset($data['is_brand']) ? $data['is_brand'] : 0,
                'video_url'        => Item::getGoodVideo($data['videoid'], Tool::getConf('hdk.url.video_url')),
                'comm_rate'       => $data['tkrates'],
                'tk_money'        => $tk_money,
                'datas'           => json_encode($data, JSON_UNESCAPED_UNICODE),

            );

            if ( $item )
            {
                Item::create()-> update($array, ['item_id' => $item_id]);
            }
            else
            {
                Item::create() -> data($array) -> save();
            }

            // 删除不需要返回给前端的字段
            if ( $is_unset )
            {
                unset($array['datas']);
                unset($array['tk_money']);
            }
        }



        return $array;

    }
}