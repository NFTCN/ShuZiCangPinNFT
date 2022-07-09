<?php

namespace app\admin\model\nft\article;

use app\admin\model\Admin;
use app\common\model\User;
use think\Model;
use traits\model\SoftDelete;

class Article extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_article';
    
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

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->field('id, nickname');
    }


}
