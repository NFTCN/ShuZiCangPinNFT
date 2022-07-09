<?php

namespace addons\nft\model;

use think\Model;

/**
 * 用户关系
 */
class UserFriend Extends Model
{

    protected $name = 'nft_user_friend';


    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    public function identify()
    {
        return $this->hasOne(Identify::class,'user_id','user_id');
        
    }

}
