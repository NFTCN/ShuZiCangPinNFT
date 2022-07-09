<?php

namespace addons\nft\model;

use think\Model;
use think\model\relation\BelongsTo;
use traits\model\SoftDelete;

class Collection extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'nft_collection';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text',
        'refreshtime_text',
        'status_text',
        'state_text',
        'pay_status',
        'level_text'
    ];
    

    public function getTypeList(): array
    {
        return ['unsaleable' => __('Type unsaleable'), 'market' => __('Type market')];
    }

    public function getStatusList(): array
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStateList(): array
    {
        return ['0' => __('State 0'), '1' => __('State 1'), '2' => __('State 2')];
    }


    public function getTypeTextAttr($value, $data): string
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }

    public function getLevelList()
    {
        return ['0' => __('Level 1'),'1' => __('Level 1'), '2' => __('Level 2'), '3' => __('Level 3')];
    }


    public function getRefreshtimeTextAttr($value, $data): string
    {
        $value = $value ?: ($data['refreshtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['level']) ? $data['level'] : '');
        $list = $this->getLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data): string
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }


    public function getStateTextAttr($value, $data): string
    {
        $value = $value ?: ($data['state'] ?? '');
        $list = $this->getStateList();
        return $list[$value] ?? '';
    }

    public function getPayStatusAttr($value,$data)
    {
        $pay_time =  strtotime($data['startdate'].' '.$data['times']);
        $pay_status = 0;
        if($pay_time > time()){
            $pay_status = 0;
        }else{
            if($data['state'] == 1){
                $pay_status = 1;
            }else{
                $pay_status = 2;
            }
        }
        return $pay_status;

    }

    protected function setRefreshtimeAttr($value): ?string
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Issuer::class, 'issuer_id', 'id');
    }


    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }
}
