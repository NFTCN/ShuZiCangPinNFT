<?php

namespace addons\nft\model;

use think\Model;

/**
 * 用户活动次数
 */
class UserActivity Extends Model
{
    protected $name = 'nft_user_activity';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

}
