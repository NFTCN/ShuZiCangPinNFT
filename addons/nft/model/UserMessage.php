<?php

namespace addons\nft\model;

use app\admin\model\nft\article\Notice;
use think\Model;
use think\model\relation\HasOne;

/**
 * oa模型
 * @property int $id 任务id
 * @property int $user_id 用户
 * @property string $title 任务标题
 * @property string $content 任务内容
 * @property int $status 任务状态
 * @property int $is_view 是否已查看
 */
class UserMessage extends Model
{

    protected $name = 'nft_user_message';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    public function getCreatetimeTextAttr($value,$data)
    {
        $value = $value?:$data['createtime']??'';
        if(is_numeric($value)){
            $value = date('m-d H:i:s',$value);
        }
        return $value;
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function message(): HasOne
    {
        return $this->hasOne(Notice::class,'id','link_id');
    }
}
