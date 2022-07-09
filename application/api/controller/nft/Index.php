<?php

namespace app\api\controller\nft;

use addons\nft\library\Common;
use app\admin\model\nft\banner\Banner;
use app\common\controller\NftApi;
use app\common\model\Version;

/**
 * 首页接口
 */
class Index extends NftApi
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index(): void
    {
        $banner_model = new Banner();
        $banner = $banner_model->field('id, article_id, image,pages')->where('status', 1)->select();
        $config = get_addon_config('nft');
        $config = $config['ini'];
        $this->success('请求成功', [
            'banner' => $banner,
            'config'=>$config
        ]);
    }

    /**
     * 隐私协议
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function privacy_protection(): void
    {
        $config = get_addon_config('nft');
        $id= $config['config']['privacy_protection']??0;
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }

    /**
     * 转赠说明
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transfer_description(): void
    {
        $config = get_addon_config('nft');
        $id= $config['marketing']['pass_on']??0;
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }

    /**
     * 用户协议
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user_agreement(): void
    {
        $config = get_addon_config('nft');
        $id= $config['config']['user_agreement']??0;
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }

    /**
     * 处罚公告
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function punish_protection(): void
    {
        $config = get_addon_config('nft');
        $id= $config['config']['punish_protection']??'';
        if(is_string($id)){
            $id = explode(',', $id);
        }
        $article = \addons\nft\model\Article::where('id','in', $id)->where('status', '1')->field('id,title,content')->select();
        $user_list = \addons\nft\model\User::where('status', 'hidden')->order('id','desc')->column('nickname');
        foreach ($user_list as &$user) {
            $user = Common::hideStr($user,2,2,4,'*');
        }
        $this->success('获取成功',['list'=>$article,'people'=>$user_list]);
    }

    /**
     * 关于我们
     */
    public function aboutus(): void
    {
        $config = get_addon_config('nft');
        $config = $config['ini'];
        $config['logo'] = cdnurl($config['logo'],true);
        $this->success('获取成功',$config);
    }

    /**
     * 转赠说明
     */
    public function make_friend(): void
    {
        $config = get_addon_config('nft');
        $id= $config['config']['make_friend']??'';
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }

    /**
     * 充值说明
     */
    public function recharge_explain(): void
    {
        $config = get_addon_config('nft');
        $id= $config['config']['recharge']??'';
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }

    public function test()
    {
        $time = strtotime('-' . 0 . ' days');
    }

    /**
     * 邀请注册页面接口
     */
    public function boxH5(): void
    {
        $config = get_addon_config('nft');
        $box_poster = cdnurl($config['ini']['poster_h5']??'',true);
        $version = Version::where('status','normal')->order('id desc')->find();
        $download_app_url = $version['downloadurl']??'';
        $this->success('获取成功',compact('box_poster','download_app_url'));
    }

    public function badge_rule()
    {
        $config = get_addon_config('nft');
        $id = $config['marketing']['badge_rule'] ?? '';
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $this->success('获取成功',['content'=>$article['content']??'']);
    }
}
