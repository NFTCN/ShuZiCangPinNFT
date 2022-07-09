<?php

namespace app\api\controller;

use addons\nft\library\job\NoJob;
use addons\nft\model\UserCollection;
use addons\nft\model\UserCollectionLog;
use app\common\controller\Api;
use app\common\service\nft\PayService;
use think\Queue;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        Queue::push(NoJob::class, [
            'id' => 35,
            'user_id' => 14,
            'order_id' => 597,
            'type' => 'pay',
        ], 'createno');
    }
}
