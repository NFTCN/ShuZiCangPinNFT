<?php

namespace addons\nft\model;

use think\Model;
use think\model\relation\BelongsTo;
use traits\model\SoftDelete;


class Order extends Model
{

    /**
     * 订单状态
     */
    const ORDER_STATUS = [
        'wait_paid' => 1,  //待支付
        'wait_receive' => 2,  //待商家接单
        'preparing' => 3,  //备菜中
        'prepared' => 4,  //备菜完成
        'finished' => 5,  //已完成
        'closed' => 6   //已关闭
    ];

    const PAY_TYPE = [
        'alipay' => 1, //支付宝
        'wechat' => 2, //微信
        'balance' => 3, //余额
    ];

    use SoftDelete;

    // 表名
    protected $name = 'nft_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $deleteTime = 'deleteTime';

    // 追加属性
    protected $append = [
        'pay_limit_time_text',
        'order_status_text'

    ];

    //:1-待支付,2-待商家接单,3-备菜中,4-备菜完成,5-已完成,6-已关闭

    /**
     * 生成唯一订单号
     * @return string
     * @throws \think\exception\DbException
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

    public function getOrderStatusTextAttr($value, $data)
    {
        $orderStatus = [
            '0' => __('待支付'),
            '1' => __('交易中'),
            '2' => __('已完成'),
            '6' => __('已关闭'),
        ];


        $value = $orderStatus[$data['order_status']];
        if ($data['pay_status'] == 3) {
            $value = $orderStatus[6];
        }
        return $value ?? '';
    }

    public function getPayLimitTimeTextAttr($value, $data)
    {
        $value = $value ?: (isset($data['pay_limit_time']) ? $data['pay_limit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function goods()
    {
        return $this->hasOne(OrderGoods::class, 'order_id', 'id');

    }
}
