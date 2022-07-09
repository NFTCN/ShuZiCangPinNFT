<?php

namespace app\api\controller\nft;

use addons\nft\library\Common;
use addons\nft\model\Author;
use app\common\controller\NftApi;

/**
 * 藏品
 */
class Collection extends NftApi
{
    protected $noNeedLogin = ['index', 'calendar', 'details', 'authorList', 'authorInfo'];
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

    /**
     * 首页
     *
     */
    public function index()
    {
        $list = $this->model->with(['author' => function ($q) {
            $q->field('id,avatar,name');
        }])
            ->whereTime('startdate', 'between', [date('Y-m-d'), date('Y-m-d',strtotime('+ 3 days'))])
            ->where(['status' => 'normal', 'state' => ['in', [1, 2]], 'type' => 'market'])
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

    /**
     * 3天以后或更长之后的藏品
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function calendar()
    {
        $list = $this->model->with(['author' => function ($q) {
            $q->field('id,avatar,name');
        }])
            ->whereTime('startdate', '+ 3 days')
            ->where(['status' => 'normal', 'type' => 'market', 'state' => ['in', [1, 2]]])
            ->order('startdate asc,times asc')
            ->select();
        $data = [];
        // echo $this->model->getLastSql();
        foreach ($list as $item) {
            $item = $item->toArray();
            $item['image'] = cdnurl($item['image'], true);
            $item['pay_time'] = strtotime($item['startdate'] . ' ' . $item['times']);
            $item['is_remind'] = $this->auth->getUser() ? ($this->auth->getUser()->getSubscription($item['id']) ? 1 : 0) : 0;
            $data[$item['startdate']][] = $item;
        }
        $list = [];
        foreach ($data as $key => $datum) {
            $list[] = [
                'date' => $key,
                'list' => $datum
            ];
        }


        $data = [
            [
                'date' => '2022-03-14',
                'list' => [
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
                        'is_remind' => 0
                    ],
                    [
                        'id' => 2,
                        'image' => cdnurl('/uploads/detailimg1.png', true),
                        'pay_time' => 0,
                        'title' => '已售罄 * 弑天妖狼',
                        'stock' => 1000,
                        'price' => 39.90,
                        'author' => [
                            'avatar' => cdnurl('/uploads/20220312115349.jpg', true),
                            'name' => '作者姓名'
                        ],
                        'is_remind' => 1
                    ]
                ]
            ]
        ];

        $this->success('请求成功', $list);
    }

    public function details($id = null)
    {

        $info = $this->model->with(['author', 'issuer'])->where('id', $id)->find();
        if (empty($info)) {
            $this->error('未找到指定藏品');
        }
        $info = $info->toArray();
        $info['image'] = cdnurl($info['image'], true);
        $info['master_image'] = cdnurl($info['master_image'], true);
        $info['pay_time'] = strtotime($info['startdate'] . ' ' . $info['times']);
        $info['is_remind'] = 0;
        $info['collection_synopsis'] = $info['description'];
        $info['author_synopsis'] = $info['author']['description'];
        $info['issuer'] = $info['issuer']['name'];
        $config = get_addon_config('nft');

        $info['notice'] = $config['ini']['warning'] ?? '';
        $info['color'] = $info['text_color'];

        $this->success('请求成功', $info);
    }

    public function addsubscription($id = null)
    {
        $redis = Common::redis();
        $redis->sAdd('subscription' . $this->auth->id, $id);
        $this->success('订阅成功');
    }

    /**
     * 艺术家列表
     * @throws \think\exception\DbException
     */
    public function authorList()
    {
        $limit = (int)$this->request->param('limit') ?: 10;
        $autuorData = Author::order('weigh desc')
            ->field('id, name, avatar, blog_address, description, praise, collect')
            ->paginate($limit)->each(function ($item) {
                $item['is_like'] = $this->auth->getUser() ? ($this->auth->getUser()->getLikeAuthor($item->id) ? 1 : 0) : 0;
                $item['is_collection'] = $this->auth->getUser() ? ($this->auth->getUser()->getCollectionAuthor($item->id) ? 1 : 0) : 0;
                return $item;
            });
        $this->success('请求成功', $autuorData);
    }

    /**
     * 点赞
     *
     * @param null $author_id
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function likeAuthor($author_id = null)
    {
        $info = Author::where('id', $author_id)->find();
        if (empty($info)) {
            $this->error('未找到作家');
        }
        try {
            $user = $this->auth->getUser();
            if ($user) {
                if ($user->getLikeAuthor($author_id)) {
                    //存在就取消
                    $user->sRemLikeAuthor($author_id);
                    $info->setDec('praise');
                } else {
                    $user->addLikeAuthor($author_id);
                    $info->setInc('praise');
                }
            }
        } catch (\Exception $e) {
            $this->error('点赞失败,请稍后尝试');
        }

        $this->success('点赞成功');
    }

    /**
     * 收藏
     *
     * @param null $author_id
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectAuthor($author_id = null)
    {
        $info = Author::where('id', $author_id)->find();
        if (empty($info)) {
            $this->error('未找到作家');
        }
        try {
            $user = $this->auth->getUser();
            if ($user) {
                if ($user->getCollectionAuthor($author_id)) {
                    //存在就取消
                    $user->sRemCollectionAuthor($author_id);
                    $info->setDec('collect');
                } else {
                    $user->addCollectionAuthor($author_id);
                    $info->setInc('collect');
                }
            }
        } catch (\Exception $e) {
            $this->error('收藏失败,请稍后尝试');
        }
        $this->success('更新成功');
    }


    /**
     * 艺术家详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function authorInfo()
    {
        $author_id = (int)$this->request->param('author_id');
        if (!$author_id) {
            $this->error('暂无法查看');
        }
        $info = Author::where('id', $author_id)
            ->field('id, name, avatar, bg_image, blog_address, wallet_address, description, praise, collect')
            ->find();
        if (!$info) {
            $this->error('暂无法查看');
        }
        // 藏品
        $collect = $this->model
            ->where('author_id', $author_id)
            ->where('status', 'normal')
            ->where('type', 'in', ['market'])
            ->field('id, author_id, title, tag, master_image, description, price, stock, state, startdate, times')
            ->select();
        foreach ($collect as &$item) {
            $item->master_image = cdnurl($item->master_image,true);
        }
        $user = $this->auth->getUser();
        $is_like = 0;
        $is_collection = 0;
        if($user){
            $is_like = $user->getLikeAuthor($author_id);
            $is_collection = $user->getCollectionAuthor($author_id);
        }

        $this->success('请求成功', ['info' => $info, 'collect' => $collect,'is_like'=>$is_like,'is_collection'=>$is_collection]);
    }
}
