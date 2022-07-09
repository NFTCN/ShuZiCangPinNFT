<?php

namespace addons\nft\model;

use think\Model;
use traits\model\SoftDelete;

class Author extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'nft_author';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'genderdata_text',
    ];
    

    public function getGenderdataList()
    {
        return ['male' => __('Genderdata male'), 'female' => __('Genderdata female')];
    }



    public function getGenderdataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['genderdata']) ? $data['genderdata'] : '');
        $list = $this->getGenderdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getAvatarAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['avatar']) ? $data['avatar'] : '');
        return cdnurl($value,true);
    }
}
