<?php

namespace app\api\controller\nft;

use app\common\controller\NftApi;
use app\common\service\nft\OrderService;

/**
 * 订单
 */
class Order extends NftApi
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];


    /**
     * 订单列表
     *
     */
    public function index()
    {
        $this->success('请求成功', OrderService::list());
    }
}
