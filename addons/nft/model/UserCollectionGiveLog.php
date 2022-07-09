<?php

namespace addons\nft\model;

use think\Model;
use traits\model\SoftDelete;

class UserCollectionGiveLog extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_user_collection_give_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    public function usercollection()
    {
        return $this->hasOne(UserCollection::class,'tokenId','tokenId');

    }
    

    







}
