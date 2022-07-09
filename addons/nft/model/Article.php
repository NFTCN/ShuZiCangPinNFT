<?php

namespace addons\nft\model;

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

}
