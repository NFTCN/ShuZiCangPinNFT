<?php

namespace app\api\controller\nft;

use addons\nft\model\UserMessage;
use app\common\controller\NftApi;

/**
 * 文章接口
 */
class Article extends NftApi
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * Article模型对象
     * @var \app\admin\model\nft\article\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\nft\article\Article;
    }

    /**
     * 文章列表
     */
    public function list()
    {
        $page = input('page/d', 1);
        $pageSize = input('pageSize/d', 10);
        $keywords = input('keywords');
        $list = $this->model->field('id, title, image, createtime')->where('status', 1)->where('title', 'like', '%' . $keywords . '%')->page($page, $pageSize);
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
            $this->error('文章不存在');
        }
        $article = $this->model->field('id, title, content, image, view_num, createtime')->where('id', $id)->where('status', 1)->find();
        if (empty($article)) {
            $this->error('文章不存在');
        }
        $article->setInc('view_num');
        $this->success('请求成功', $article);
    }

    public function notice_detail()
    {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('通知不存在');
        }
        $info = UserMessage::where('id',$id)->find();
        if (empty($info)) {
            $this->error('通知不存在');
        }
        $info->is_view = 1;
        $info->save();

        $this->success('请求成功', $info->append(['message', 'createtime_text']));

    }
}
