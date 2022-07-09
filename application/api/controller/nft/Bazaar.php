<?php

namespace app\api\controller\nft;

use addons\nft\library\Common;
use addons\nft\model\Category;
use app\common\controller\NftApi;

/**
 * 市场
 */
class Bazaar extends NftApi
{
    protected $noNeedLogin = ['category','index'];
    protected $noNeedRight = ['*'];
    /**
     * @var \addons\nft\model\Collection
     */
    private $model;

    public function __construct()
    {
        $this->model = new \addons\nft\model\Collection();
        parent::__construct();
    }

    public function category()
    {
        $list = model(Category::class)->where(['type'=>'series','pid'=>0,'status'=>'normal'])->field('id,name,image')->select();
        $this->success('获取成功',$list);
    }

    /**
     *市场藏品
     */
    public function index()
    {
        $category_id = input('category_id');

        $list = $this->model->with(['author' => function ($q) {
            $q->field('id,avatar,name');
        }])
            ->where(function ($q) use ($category_id){
                if(!empty($category_id)){
                    $q->where('category_id',$category_id);
                }
            })
            ->where(['status' => 'normal', 'state' => ['in', [1, 2]], 'type' => 'bazaar'])
            ->order('weigh desc')
            ->paginate(input('limit', 10))->each(function ($item) {
                $item['image'] = cdnurl($item['image'], true);
                $item['pay_time'] = strtotime($item['startdate'] . ' ' . $item['times']);
                $item['image'] = cdnurl($item['image'], true);
                $item['is_remind'] = $this->auth->getUser() ? ($this->auth->getUser()->getSubscription($item->id) ? 1 : 0) : 0;
                return $item;
            });
            // echo $this->model->getLastSql();
        $data = [
            'total' => 1,
            'per_page' => 10,
            'current_page' => 1,
            'last_page' => 1,
            'data' => [
                [
                    'id' => 1,
                    'image' => cdnurl('/uploads/detailimg1.png', true),
                    'pay_time' => time() + 84600,
                    'title' => '画作标题',
                    'stock' => 1000,
                    'price' => 39.90,
                    'author' => [
                        'avatar' => cdnurl('/uploads/20220312115349.jpg', true),
                        'name' => '作者姓名'
                    ],
                    'is_remind' => 0,
                    'pay_status' => 0
                ],
                [
                    'id' => 2,
                    'image' => cdnurl('/uploads/detailimg1.png', true),
                    'pay_time' => time(),
                    'title' => '开售 * 弑天妖狼',
                    'stock' => 1000,
                    'price' => 39.90,
                    'author' => [
                        'avatar' => cdnurl('/uploads/20220312115349.jpg', true),
                        'name' => '作者姓名'
                    ],
                    'is_remind' => 1,
                    'pay_status' => 1
                ],
                [
                    'id' => 3,
                    'image' => cdnurl('/uploads/detailimg1.png', true),
                    'pay_time' => time() - 84600,
                    'title' => '画作标题',
                    'stock' => 1000,
                    'price' => 39.90,
                    'author' => [
                        'avatar' => cdnurl('/uploads/20220312115349.jpg', true),
                        'name' => '作者姓名'
                    ],
                    'is_remind' => 0,
                    'pay_status' => 2
                ],
            ]
        ];

        $this->success('请求成功', $list);
    }
}
