<?php

namespace app\admin\model\nft\pay;

use app\common\service\nft\PayService;
use think\Model;


class Log extends Model
{

    

    

    // 表名
    protected $name = 'nft_pay_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function getStatusList()
    {
        return ['0' => __('未支付'), '1' => __('已支付')];
    }

    public function getTypeList()
    {
        return [
            PayService::TYPE_ALI_PAY=> __('支付宝'),
            PayService::TYPE_WX_PAY => __('微信'),
            PayService::TYPE_BALANCE => __('余额'),
        ];
    }
    

    







    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
