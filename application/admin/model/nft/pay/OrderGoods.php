<?php

namespace app\admin\model\nft\pay;

use think\Model;
use traits\model\SoftDelete;

class OrderGoods extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_order_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'refund_status_text',
        'status_text'
    ];
    

    
    public function getRefundStatusList()
    {
        return ['0' => __('Refund_status 0'), '1' => __('Refund_status 1'), '2' => __('Refund_status 2'), '3' => __('Refund_status 3'), '4' => __('Refund_status 4'), '5' => __('Refund_status 5')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getRefundStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refund_status']) ? $data['refund_status'] : '');
        $list = $this->getRefundStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
