<?php

namespace addons\nft\model;

use think\Model;

/**
 * 会员余额日志模型
 */
class MoneyLog extends Model
{

    // 表名
    const PAY = 'pay';
    const RECHARGE = 'recharge';
    const WITHDRAW = 'withdraw';
    const MEMOLIST = [
        self::PAY => '消费',
        self::RECHARGE => '充值',
        self::WITHDRAW => '提现'
    ];

    protected $name = 'user_money_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
        'memo_text',
        'createtime_text'
    ];

    public function getMemoTextAttr($value, $data)
    {
        $value = $value ?: $data['memo'];
        return self::MEMOLIST[$value];
    }

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ?: $data['createtime'];
        return date('Y-m-d H:i:s', $value);
    }

}
