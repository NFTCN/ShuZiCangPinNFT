<?php

namespace app\admin\model\nft\banner;

use addons\nft\model\Article;
use think\Model;


class Banner extends Model
{

    

    

    // 表名
    protected $name = 'nft_banner';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'pages_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }

    public function getPagesList()
    {
        return ['activity' => __('Pages activity'), 'article' => __('Pages article')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPagesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pages']) ? $data['pages'] : '');
        $list = $this->getPagesList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getImageAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        return cdnurl($value,true);
    }


    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
