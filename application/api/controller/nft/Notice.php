<?php

namespace app\api\controller\nft;

use app\admin\model\nft\article\NoticePeople;
use app\common\controller\NftApi;

/**
 * 公告
 */
class Notice extends NftApi
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * Notice模型对象
     * @var \app\admin\model\nft\article\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\nft\article\Notice();
    }

    /**
     * 公告列表
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list()
    {
        $page = input('page/d', 1);
        $pageSize = input('pageSize/d', 10);
        $keywords = input('keywords');
        $list = $this->model->field('id, title, image, createtime')
            ->where('status', 1)
            ->where('title', 'like', '%' . $keywords . '%')
            ->page($page, $pageSize);
        $this->success('请求成功', [
            'list' => $list->select(),
            'pages' => [
                'total' => $list->count(),
                'page' => $page,
                'pageSize' => $pageSize
            ]
        ]);
    }

    /**
     * 文章详情
     */
    public function detail()
    {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('公告不存在');
        }
        $article = $this->model->field('id, title, content, image, view_num, createtime')->where('id', $id)->where('status', 1)->find();
        if (empty($article)) {
            $this->error('公告不存在');
        }
        $article->setInc('view_num');
        NoticePeople::add($this->auth->id,$id); //添加查看历史记录
        $this->success('请求成功', $article);
    }
}
