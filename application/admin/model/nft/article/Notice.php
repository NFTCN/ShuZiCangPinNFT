<?php

namespace app\admin\model\nft\article;

use think\Model;
use traits\model\SoftDelete;

class Notice extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function log()
    {
        return $this->hasOne(NoticePeople::class,'notice_id','id');
        
    }




}
