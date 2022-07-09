<?php

namespace addons\nft\model;

use think\Db;
use think\Model;

/**
 * 会员身份信息
 */
class Identify extends Model
{

    protected $name = 'nft_user_identify';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    // 追加属性
    protected $append = [
    ];

}
