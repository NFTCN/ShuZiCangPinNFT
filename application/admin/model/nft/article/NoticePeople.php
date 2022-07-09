<?php

namespace app\admin\model\nft\article;

use think\Model;
use traits\model\SoftDelete;

class NoticePeople extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_notice_people';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    public static function add($user_id, $id)
    {
        $model = self::where(['user_id'=>$user_id,'notice_id'=>$id])->count();
        if(empty($model)){
            self::create(['user_id'=>$user_id,'notice_id'=>$id]);
        }
    }
}
