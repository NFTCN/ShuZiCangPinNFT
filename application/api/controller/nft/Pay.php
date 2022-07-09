<?php

namespace app\api\controller\nft;

use addons\epay\library\Service;
use addons\nft\model\Order;
use addons\nft\model\OrderGoods;
use addons\nft\model\RechargeOrder;
use app\common\controller\NftApi;
use app\common\service\nft\OrderService;
use app\common\service\nft\PayService;
use app\admin\model\nft\pay\Log as PayLog;
use Symfony\Component\HttpFoundation\Request;
use think\Cache;
use think\Db;
use think\Exception;

/**
 * 支付
 */
class Pay extends NftApi
{
    protected $noNeedLogin = ['index', 'notify','alipayreturn','checkPayStatus'];
    protected $noNeedRight = ['*'];


    public function __construct()
    {
        parent::__construct();
        $arr = array_map('strtolower', $this->noNeedLogin);
        if ((!in_array(strtolower($this->request->action()), $arr) || in_array('*', $arr))) {
            //需要实名认证
            $config = get_addon_config('nft');
            if($config['ini']['identify']){
                if (empty($this->auth->getUser()->identify)) {
                    $this->error('请先完成实名制', null, 402);
                }
            }
        }
    }

    /**
     * 支付配置
     *
     */
    public function index()
    {
        $pay_model = Cache::get('pay_config');
        if (empty($pay_model)) {
            $pay_model = [];
            $config = get_addon_config('epay');
            $nft_config = get_addon_config('nft');
            foreach ($config as $key => $item) {
                switch ($this->client) {
                    case 'mp':
                        if (!empty($item['miniapp_id']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //微信小程序支付
                            $pay_model[] = [
                                'title' => '微信支付',
                                'method' => $key,
                            ];
                        }
                        break;
                    default:
                        if (!empty($item['appid']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '微信',
                                'method' => $key,
                            ];
                        }
                        if ($key == 'alipay' && !empty($item['app_id']) && !empty($nft_config['ini']['alipay_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '支付宝',
                                'method' => $key,
                            ];
                        }
                }
            }
            if (!empty($nft_config['ini']['balance_pay'])) {
                $pay_model[] = [
                    'title' => '余额',
                    'method' => PayService::TYPE_BALANCE,
                ];
            }
            Cache::set('pay_config', $pay_model, 300);
        }
        $this->success('请求成功', $pay_model);
    }

    /**
     * 充值配置
     *
     */
    public function recharge_type()
    {
        $pay_model = Cache::get('recharge_type_config');
        if (empty($pay_model)) {
            $pay_model = [];
            $config = get_addon_config('epay');
            $nft_config = get_addon_config('nft');
            foreach ($config as $key => $item) {
                switch ($this->client) {
                    case 'mp':
                        if (!empty($item['miniapp_id']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //微信小程序支付
                            $pay_model[] = [
                                'title' => '微信支付',
                                'method' => $key,
                            ];
                        }
                        break;
                    default:
                        if (!empty($item['appid']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '微信',
                                'method' => $key,
                            ];
                        }
                        if ($key == 'alipay' && !empty($item['app_id']) && !empty($nft_config['ini']['alipay_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '支付宝',
                                'method' => $key,
                            ];
                        }
                }
            }
            Cache::set('recharge_type_config', $pay_model, 300);
        }
        $this->success('请求成功', $pay_model);
    }

    /**
     * 提现配置
     *
     */
    public function withdraw_type()
    {
        $pay_model = Cache::get('withdraw_type_config');
        if (empty($pay_model)) {
            $pay_model = [];
            $config = get_addon_config('epay');
            $nft_config = get_addon_config('nft');
            foreach ($config as $key => $item) {
                switch ($this->client) {
                    case 'mp':
                        if (!empty($item['miniapp_id']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //微信小程序支付
                            $pay_model[] = [
                                'title' => '微信支付',
                                'method' => $key,
                            ];
                        }
                        break;
                    default:
                        if (!empty($item['appid']) && !empty($nft_config['ini']['wechat_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '微信',
                                'method' => $key,
                            ];
                        }
                        if ($key == 'alipay' && !empty($item['app_id']) && !empty($nft_config['ini']['alipay_pay'])) {
                            //app支付
                            $pay_model[] = [
                                'title' => '支付宝',
                                'method' => $key,
                            ];
                        }
                }
            }
            Cache::set('withdraw_type_config', $pay_model, 300);
        }
        $this->success('请求成功', $pay_model);
    }

    /**
     * 检查商品
     */
    public function checkCollection(): void
    {
        ///设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isGet()) {
            $collection_id = input('collection_id');

            if (empty($collection_id)) {
                $this->error('未扎到藏品信息');
            }

            $collection = \addons\nft\model\Collection::where('id', $collection_id)->where(['status' => 'normal', 'state' => 1])->find();
            if (empty($collection)) {
                $this->error('已售罄');
            }
            try {
                $key = 'collection_' . $collection_id;
                if (!PayService::checkoutStock($key, 1)) {
                    $collection->state = 2;
                    $collection->save();
                    $this->error('已售罄');
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $this->error('系统错误');
            }
            //检查购买数量限制
            if(!empty($collection->pay_limit)){
                //有限制 查看用户购买过多少个同类藏品
                $order_ids = Order::where(['user_id'=>$this->auth->id,'pay_status'=>1])->column('id');
                if(!empty($order_ids)){
                    $count = OrderGoods::where(['goods_id'=>$collection_id,'goods_status'=>1])->where('order_id','in',$order_ids)->count();
                    if($count > $collection->pay_limit){
                        $this->error('每人只能购买'.$collection->pay_limit.'个');
                    }
                }
            }

            // 传递Token
            $datalist['token'] = PayService::creatToken();

            $info = [
                'id' => $collection->id,
                'image' => cdnurl($collection->image, true),
                'title' => $collection->title,
                'price' => $collection->price,
                'author' => [
                    'avatar' => cdnurl($collection->author->avatar, true),
                    'name' => $collection->author->name
                ],
            ];
            // 订单
            $datalist['orderData'] = $info;
            try {
                Cache::set($datalist['token'] . $this->auth->id, json_encode($datalist), 15 * 60);
            } catch (\JsonException $e) {
                $this->error('商品数据异常');
            }
            $this->success('ok', $datalist);
        } else {
            $this->error(__('非法请求'));
        }
    }

    /**
     * 创建支付订单
     * @throws Exception
     */
    public function addOrder()
    {
        /**
         * 1.检查库存
         * 2.生成预订单
         */
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $collection_id = $params['collection_id']; //藏品id
            $user_id = $this->auth->id; // 用户ID
            $key = $params['order_token'] . $this->auth->id;
            try {
                $data = json_decode(Cache::get($key), true, 512);
            } catch (\JsonException $e) {
                $this->error('订单超时,请重新下单');
            }
            if (empty($data)) {
                $this->error('请勿重复提交订单');
            }

            try {
                $stock_key = 'collection_' . $collection_id;
                if (!PayService::checkoutStock($stock_key, 1)) {
                    $this->error('已售罄');
                }
                PayService::subStock($stock_key, 1);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $this->error('系统错误');
            }
            $config = get_addon_config('nft');

            // 数据库事务操作
            Db::startTrans();
            try {
                // 生成订单
                $order = new \addons\nft\model\Order();
                $order->user_id = $user_id;
                $order->order_no = \addons\nft\model\Order::createOrderNo();
                $order->order_status = 0;
                $order->order_type = $params['order_type'] ?? 1;
                $order->remarks = $params['remarks'] ?? '';
                $order->pay_status = 0;
                $order->pay_price = $data['orderData']['price'];
                $order->pay_limit_time = time() + (($config['ini']['pay_limit_time']??15) * 60);

                if (!$order->save()) {
                    $this->error('订单创建失败');
                }
                $goodsList = [];
                $goodsList[] = [
                    'order_id' => $order->id,
                    'goods_id' => $collection_id,
                    'title' => $data['orderData']['title'],
                    'image' => $data['orderData']['image'],
                    'price' => $data['orderData']['price'],
                    'number' => 1,
                ];

                model(OrderGoods::class)->saveAll($goodsList);
                Db::commit();
            } catch (\PDOException | \Exception $e) {
                Db::rollback();
                PayService::addStock($stock_key, 1);
                $this->error($e->getMessage() . $e->getLine());
            }

            //减少库存
            $order = $order->toArray();
            $order['prescription'] = $order['pay_limit_time'] - time();
            Cache::rm($key);
            $this->success('订单创建成功', $order);
        } else {
            $this->error(__('非法请求'));
        }
    }


    /**
     * 获取支付参数
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pay()
    {
        $order_no = $this->request->post('order_no');
        $pay_type = $this->request->post('pay_type');
        if (empty($order_no) || empty($pay_type)) {
            $this->error('参数错误');
        }
        $method = 'app';
        $openid = '';
        if ($this->client === 'mp') {
            $method = 'miniapp';
            //微信支付需要微信小程序openid
            $openid = Service::getOpenid($method);
            if (empty($openid)) {
                $this->error('请检查微信支付配置');
            }
        } elseif ($this->client == 'wap') {
            $method = 'wap';
        }
        $order = model(\addons\nft\model\Order::class)->where('order_no', $order_no)->where(['pay_status' => 0, 'order_status' => 0])->find();
        if (empty($order)) {
            $this->error('订单已失效或已支付');
        }
        if($pay_type == PayService::TYPE_BALANCE){
            //余额支付 判断是足够
            if($this->auth->getUser()->money < $order->pay_price){
                $this->error('余额不足');
            }
        }
        $params = [
            'amount' => $order->pay_price,
            'openid' => $openid,
            'orderid' => $order->id,
            'order_no' => $order->order_no,
            'type' => $pay_type,
            'title' => '订单支付',
            'method' => $method,
            'user_id' => $this->auth->id,
            'event' => [OrderService::class, 'payCallback'],
        ];
        try {
            $this->success('请求成功', PayService::run($params));
        } catch (Exception $e) {
            $this->error('支付失败');
        }
    }

    /**
     * 支付回调
     */
    public function notify()
    {
        $paytype = $this->request->param('paytype');
        $pay = PayService::checkNotify($paytype);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        try {
            PayService::notify([
                'type' => $paytype
            ]);
        } catch (\Exception $e) {
        }
        echo $pay->success();
    }


    /**
     * 用户充值
     */
    public function recharge()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $money = $this->request->post('money');
            $pay_type = $this->request->post('pay_type');
            $pay_type ? $pay_type : ($this->error(__('未选择支付类型')));
            $money ? $money : ($this->error(__('为输入充值金额')));
            if (empty($this->auth->id)) {
                $this->error('用户不存在');
            }
            if ($money <= 0) {
                $this->error('充值金额不合法');
            }
            $method = 'app';
            $openid = '';
            if ($this->client === 'mp') {
                $method = 'miniapp';
                //微信支付需要微信小程序openid
                $openid = Service::getOpenid($method);
                if (empty($openid)) {
                    $this->error('请检查微信支付配置');
                }
            } elseif ($this->client == 'wap') {
                $method = 'wap';
            }

            // 充值订单号
            $pay_no = date("Ymdhis") . sprintf("%08d", $this->auth->id) . mt_rand(1000, 9999);
            // 支付标题
            $title = '充值-' . $pay_no;
            // 生成一个订单
            $order = RechargeOrder::create([
                'order_no' => $pay_no,
                'user_id' => $this->auth->id,
                'pay_price' => $money,
                'payamount' => 0,
                'pay_type' => $pay_type,
                'ip' => $this->request->ip(),
                'order_status' => 0
            ]);
            $params = [
                'amount' => $order->pay_price,
                'openid' => $openid,
                'orderid' => $order->id,
                'order_no' => $order->order_no,
                'type' => $pay_type,
                'title' => '用户充值',
                'method' => $method,
                'user_id' => $this->auth->id,
                'event' => [OrderService::class, 'rechargeCallback'],
                'ref_type' => 'recharge'
            ];
            try {
                $this->success('请求成功', PayService::run($params));
            } catch (Exception $e) {
                $this->error('支付失败');
            }
        }
        $this->error(__('非正常请求'));
    }


    /**
     * H5支付页面  为了体验在此不做验证 只做重定向
     */
    public function alipayreturn()
    {
        $out_trade_no = input('out_trade_no'); //支付订单号
        $domain = $this->request->root(true).'/web/index.html#/pages/addpages/payOK/payOK';
        return redirect($domain.'?out_trade_no='.$out_trade_no,[],302);
    }

    public function checkPayStatus()
    {
        $out_trade_no = input('out_trade_no'); //支付订单号
        $logModel = PayLog::get([
            'order_id' => $out_trade_no
        ]);
        if(empty($logModel)){
            $this->error('订单不存在');
        }
        $order = Order::get(['id' => $logModel->order_id, 'order_no' => $logModel->out_trade_no]);
        $this->success('获取成功',$order);
    }

}
