<?php

namespace app\admin\model\nft;

use app\common\service\nft\PayService;
use Cassandra\FutureClose;
use think\Cache;
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
        'state_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
            //设置库存
            PayService::setStock('collection_'.$row[$pk], $row->stock);
            Cache::set('collection_'.$row[$pk] . 'no', 0); //设置编号标记
        });

        self::afterDelete(function ($row){
            $pk = $row->getPk();
            //删除库存
            PayService::rmStock('collection_'.$row[$pk]);
            Cache::rm('collection_'.$row[$pk] . 'no'); //删除编号标记

        });
    }

    
    public function getTypeList(): array
    {
        return ['unsaleable' => __('Type unsaleable'), 'market' => __('Type market'),'bazaar'=>'市场售卖','badge'=>'徽章'];
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


    public function getRefreshtimeTextAttr($value, $data): string
    {
        $value = $value ?: ($data['refreshtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
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

    protected function setRefreshtimeAttr($value): ?string
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function issuer(): BelongsTo
    {
        return $this->belongsTo('Issuer', 'issuer_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function author(): BelongsTo
    {
        return $this->belongsTo('Author', 'author_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
