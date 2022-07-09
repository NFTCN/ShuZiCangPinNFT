<?php

namespace app\admin\model\nft\pay;

use think\Model;


class Order extends Model
{

    // 表名
    protected $name = 'nft_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'delivery_time_text',
        'original_preparation_time_text',
        'preparation_time_text',
        'pay_limit_time_text',
        'finish_time_text',
        'protect_limit_time_text',
        'merchant_receive_time_text',
        'merchant_prepared_time_text',
        'rider_receive_time_text',
        'arrival_time_text',
        'delivered_time_text',
        'expected_delivered_time_text',
        'close_time_text',
        'order_status_text'
    ];

    public function getStateList()
    {
        return ['1' => __('待支付'), '2' => __('待商家接单'), '3' => __('备菜中'), '4' => __('备菜完成'), '5' => __('已完成')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    //:1-待支付,2-待商家接单,3-备菜中,4-备菜完成,5-已完成,6-已关闭
    public function getOrderStatusTextAttr($value, $data)
    {
        $orderStatus = [
            '1' => __('待支付'),
            '2' => __('待商家接单'),
            '3' => __('已完成'),
            '-1' => __('已关闭'),
        ];
        $value = $orderStatus[$data['order_status']];
        return $value ?? '';
    }


    public function getDeliveryTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delivery_time']) ? $data['delivery_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getOriginalPreparationTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['original_preparation_time']) ? $data['original_preparation_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPreparationTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['preparation_time']) ? $data['preparation_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPayLimitTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_limit_time']) ? $data['pay_limit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFinishTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['finish_time']) ? $data['finish_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getProtectLimitTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['protect_limit_time']) ? $data['protect_limit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getMerchantReceiveTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['merchant_receive_time']) ? $data['merchant_receive_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getMerchantPreparedTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['merchant_prepared_time']) ? $data['merchant_prepared_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getRiderReceiveTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['rider_receive_time']) ? $data['rider_receive_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getArrivalTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['arrival_time']) ? $data['arrival_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getDeliveredTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delivered_time']) ? $data['delivered_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getExpectedDeliveredTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expected_delivered_time']) ? $data['expected_delivered_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getCloseTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['close_time']) ? $data['close_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setDeliveryTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setOriginalPreparationTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPreparationTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPayLimitTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setFinishTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setProtectLimitTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setMerchantReceiveTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setMerchantPreparedTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setRiderReceiveTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setArrivalTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setDeliveredTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setExpectedDeliveredTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setCloseTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public static function getCode($shop_id)
    {
        $beginTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        return (int)self::where([
                'shop_id' => $shop_id,
                'created_at' => ['between', [$beginTime, $endToday]]
            ])->count() + 1;
    }


    /**
     * 生成唯一订单号
     * @return string
     */
    public static function createOrderNo(): string
    {
        $orderNo = '';
        while (true) {
            $no = time() . rand(1000000000, 9999999999);
            $res = self::get(['order_no' => $no]);
            if (!empty($res)) {
                continue;
            }
            $orderNo = $no;
            break;
        }
        return $orderNo;
    }

    public function shop()
    {
        return $this->belongsTo('app\admin\model\oto\shop\Shop', 'shop_id', 'id', [], 'LEFT');
    }

    public function address()
    {
        return $this->belongsTo('app\admin\model\oto\OrderAddress', 'id', 'order_id', [], 'LEFT');
    }
    public function task()
    {
        return $this->belongsTo('app\admin\model\oto\pond\Task', 'order_no', 'orderId', [], 'LEFT');
    }


    public function goods()
    {
        return $this->belongsTo('app\admin\model\oto\OrderGoods', 'order_id', 'id', [], 'LEFT');
    }

    public function user()
    {
        return $this->belongsTo('app\common\model\user', 'user_id', 'id', [], 'LEFT');
    }

    const DELIVERY_STATUS = [
        'none' => 0,  //非配送订单
        'wait_receive' => 1,  //待骑手接单
        'received' => 2,  //骑手已接单
        'picked' => 3,  //骑手已取货
        'delivered' => 4,  //骑手已送达
        'merchant' => 5,  //商家配送
    ];

    /**
     * 订单状态
     */
    const ORDER_STATUS = [
        'wait_paid'     => 1,  //待支付
        'wait_receive'  => 2,  //待商家接单
        'preparing'     => 3,  //备菜中
        'prepared'      => 4,  //备菜完成
        'finished'      => 5,  //已完成
        'closed'        => 6   //已关闭
    ];

    /**
     * 关闭类型
     */
    const CLOSE_TYPE = [
        'customer' => 1,    //买家取消
        'merchant' => 2,    //卖家关闭(取消并退款)
        'timeout' => 3,    //超时未支付
        'merchant_timeout' => 4,    //商家接单超时
        'plat_join' => 5,    //平台介入
    ];

    const PAY_TYPE = [
        'alipay' => 1, //支付宝
        'wechat' => 2, //微信
        'balance' => 3, //余额
    ];

    /**
     * 付款成功订单状态
     */
    const ORDER_STATUS_PAID = [
        self::ORDER_STATUS['wait_receive'],
        self::ORDER_STATUS['preparing'],
        self::ORDER_STATUS['prepared'],
        self::ORDER_STATUS['finished'],
    ];

    /**
     * 待收货订单状态
     */
    const ORDER_STATUS_WAITING = [
        self::ORDER_STATUS['preparing'],
        self::ORDER_STATUS['prepared'],
    ];

    /**
     * 配送方式
     */
    const DELIVERY_METHOD = [
        'delivery' => 1, //骑手配送
        'arrival'  => 2, //到店取货
        'merchant' => 3, //商家配送
    ];
}
