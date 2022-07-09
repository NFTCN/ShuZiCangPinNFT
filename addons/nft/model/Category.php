<?php

namespace addons\nft\model;

use think\Model;

/**
 * 分类模型
 */
class Category extends Model
{

    protected $name = 'nft_category';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    public function getImageAttr($value,$data)
    {
        $value =  $value ? $value : $data['image'];
        return cdnurl($value,true);
    }
}
