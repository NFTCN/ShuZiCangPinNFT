<?php


namespace app\common\service\nft;


use addons\nft\library\Auth;
use addons\nft\library\job\NoJob;
use addons\nft\model\Collection;
use addons\nft\model\MoneyLog;
use addons\nft\model\Order;
use addons\nft\model\OrderGoods;
use addons\nft\model\RechargeOrder;
use addons\nft\model\User;
use think\Cache;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\Log;
use think\Queue;

class OrderService
{
    /**
     * 订单详情
     *
     * @param $id
     * @param int $user_id
     *
     * @return array
     * @throws Exception
     * @throws DbException
     */
    public static function detail($id, $user_id = 0)
    {
        $where = [
            'id' => $id,
            'is_delete' => 0,
        ];
        if ($user_id > 0) {
            $where['user_id'] = $user_id;
        }
        $order = Order::get($where);
        if (!$order) {
            throw new Exception('订单不存在');
        }
        $order = $order->toArray();
        $order['goods'] = model(OrderGoods::class)
            ->where(['order_id' => $id])
            ->select();
        $order['prescription'] = $order['pay_limit_time'] > $order['created_at'] ? $order['pay_limit_time'] - $order['created_at'] : 0;
        return $order;
    }

    /**
     * 支付成功后业务处理
     *
     * @param $params
     *
     * @return bool
     * @throws Exception
     */
    public static function payCallback($params)
    {
        if ($params['status'] !== 1) {
            return false;
        }
        $order = Order::get(['id' => $params['order_id'], 'order_no' => $params['out_trade_no']]);
        if (!$order) {
            return false;
        }
        Db::startTrans();
        try {
            Log::write('支付订单');
            $order->order_status = 1;
            $order->pay_status = 1;
            $order->pay_type = isset(Order::PAY_TYPE[$params['type']]) ? Order::PAY_TYPE[$params['type']] : '';
            $order->pay_time = date('Y-m-d H:i:s', $params['trade_payment']);
            $order->pay_price = $params['amount'];
            $order->save();
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }

        try {
            Log::write('生成交易链' . json_encode($order));
            $goods = OrderGoods::where(['order_id' => $order->id])->select();
            foreach ($goods as $v) {
                Queue::push(NoJob::class, [
                    'id' => $v->goods_id,
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'type' => 'pay',
                ], 'createno');
            }
        } catch (\Exception$e) {
            Log::info($e->getMessage());
        }
        return true;

    }

    /**
     * 充值回调
     * @param $params
     */
    public static function rechargeCallback($params)
    {
        if ($params['status'] !== 1) {
            return false;
        }
        $order = RechargeOrder::get(['id' => $params['order_id'], 'order_no' => $params['out_trade_no']]);
        if (!$order) {
            return false;
        }
        Db::startTrans();
        try {
            Log::write('支付订单');
            $order->order_status = 1;
            $order->pay_status = 1;
            $order->pay_type = isset(Order::PAY_TYPE[$params['type']]) ? Order::PAY_TYPE[$params['type']] : '';
            $order->pay_time = date('Y-m-d H:i:s', $params['trade_payment']);
            $order->payamount = $params['amount'];
            $order->save();
            User::money($params['amount'], $order->user_id, MoneyLog::RECHARGE);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }
        return true;
    }


    /**
     * 获取用户的订单列表
     */
    public static function list()
    {
        $state = input('state', 1);
        $auth = Auth::instance();
        $where = [];
        $where['user_id'] = $auth->id ?? 0;
        switch ($state) {
            case 0:
            case 1:
            case 2:
                $where['order_status'] = $state;
                break;
        }
        return Order::with('goods')
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit', 10))
            ->each(function ($item) {
                if ($item['pay_status']) {
                    $item['prescription'] = ($item['pay_limit_time'] > $item['created_at'] && $item['pay_limit_time'] > time()) ? $item['pay_limit_time'] - time() : 0;
                }
                $item['user_collection_id'] = 10;
                return $item;
            });
    }

    public static function overtime()
    {

        $list = Order::with('goods')->where('pay_limit_time', '<', time())
            ->where('pay_status', 0)
            ->select();
        foreach ($list as $item) {
            $item->pay_status = 3;
            $item->order_status = -1;
            $res = $item->save();
            if ($res) {
                //退还商品库存
                $stock_key = 'collection_' . $item->goods->goods_id;
                if(Cache::has($stock_key. 'stock')){
                    $collection_stock = Cache::get($stock_key. 'stock');
                }else{
                    $collection_stock = Collection::where(['id' => $item->goods->goods_id])->value('stock');
                    Cache::set($stock_key. 'stock',$collection_stock);
                }
                $stock = PayService::getStock($stock_key);
                $market = Cache::get($stock_key . 'no',0);
                if($collection_stock > bcadd($stock,$market)){
                    PayService::addStock($stock_key,1);
                }
            }
        }
    }

}
