<?php

namespace app\api\controller\nft;

use addons\nft\model\UserCollection;
use app\common\controller\NftApi;
use app\common\service\nft\PayService;
use think\Cache;
use think\Db;

/**
 * 徽章
 */
class Badge extends NftApi
{
    protected $noNeedLogin = [];
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

    public function details($link_md5 = null)
    {
        $info = UserCollection::where('tokenId', $link_md5)->find();
        if (empty($info)) {
            $this->error('未拥有藏品');
        }
        $activitybadge = $this->auth->activitybadge;
        if (empty($activitybadge)) {
            $activitybadge = 0;
        } else {
            $activitybadge = $activitybadge->num;
        }

        $info = $info->toArray();
        $info['image'] = cdnurl($info['image'], true);
        $config = get_addon_config('nft');
        $badge_up_number = $config['marketing']['badge_up_number'] ?? 3;

        //计算完成比例
        if ($activitybadge == 0) {
            $info['ratio'] = 0;
        } else {
            $info['ratio'] = bcmul(bcdiv($activitybadge, $badge_up_number), 100);
            if ($info['ratio'] > 100) {
                $info['ratio'] = 100;
            }
        }
        $up_badge_text = $config['marketing']['up_badge_text'] ?? '';
        if(!empty($up_badge_text)){
            $up_badge_text = str_replace('xx',$info['title'],$up_badge_text);
        }
        $next_level = $config['marketing']['up_badge_id'];
        if($info->collection_id == $config['marketing']['up_badge_id']){
            $next_level = 0;
        }
        $info['rule_message'] = [
            'described' => $up_badge_text,
            'rule' => '邀请' . $badge_up_number . '个好友实名认证',
            'next_level' => $next_level
        ];
        $info['compoud_status'] = 0;
        if (!empty($config['marketing']['up_badge_id'])) {
            if (UserCollection::where(['user_id' => $this->auth->id, 'collection_id' => $config['marketing']['up_badge_id']])->count() > 3) {
                $info['compoud_status'] = 1;
            }
        }


        //规则
        $id = $config['marketing']['badge_rule'] ?? '';
        $article = \addons\nft\model\Article::where('id', $id)->find();
        $info['rule'] = $article['content'] ?? '';

        $this->success('请求成功', $info);
    }

    public function compound()
    {
        $level_id = input('level_id');
        $composition = input('composition');
        if (empty($level_id)) {
            $this->error('缺少要合成的等级');
        }
        if(!empty($composition)){
            $this->error('缺少材料');
        }
        if(UserCollection::where('tokenId',$composition)->count() == 0){
            $this->error('缺少材料');
        }
        $activitybadge = $this->auth->activitybadge;
        if (empty($activitybadge)) {
            $activitybadge = 0;
        } else {
            $activitybadge = $activitybadge->num;
        }
        if ($activitybadge < 3) {
            $this->error('铸造失败,未达到要求');
        }

        $stock_key = 'collection_' . $level_id;
        if (!PayService::checkoutStock($stock_key, 1)) {
            $this->error('徽章已发放完');
        }


        //开始铸造
        Db::startTrans();
        try {
            //扣除条件次数
            $this->auth->activitybadge->setDec('num', 3);
            //开始生成编号
            if (Cache::has($stock_key . 'no')) {
                $test_no = Cache::inc($stock_key . 'no');
            } else {
                $market = UserCollection::where('collection_id', $level_id)->count();
                $test_no = bcadd($market, 1);
                Cache::set($stock_key . 'no', $test_no);
            }
            $link_md5 = UserCollection::addBadgeLog($level_id, $this->auth->id,$test_no);
            //合成成功 销毁材料
            $res = UserCollection::where('hash_no',$composition)->where('user_id',$this->auth->id)->update(['user_id'=>0]);
            if(!$res){
                exception('铸造失败');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('铸造失败');
        }

        PayService::subStock($stock_key,1);
        \addons\nft\model\Collection::where('id',$level_id)->setInc('market');

        $this->success('铸造成功',['link_md5'=>$link_md5]);
    }
}
