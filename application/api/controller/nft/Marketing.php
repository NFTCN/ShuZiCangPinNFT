<?php

namespace app\api\controller\nft;

use addons\nft\library\job\NoJob;
use addons\nft\model\AirDrop;
use addons\nft\model\Box;
use addons\nft\model\UserBox;
use addons\nft\model\UserCollection;
use addons\nft\model\UserFriend;
use app\common\controller\NftApi;
use app\common\service\nft\PayService;
use think\Db;
use think\Exception;
use think\Log;
use think\Queue;

/**
 * 营销接口
 */
class Marketing extends NftApi
{
    protected $noNeedLogin = ['box'];
    protected $noNeedRight = ['*'];

    /**
     * Article模型对象
     * @var \app\admin\model\nft\article\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 盲盒玩法
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function box()
    {
        $user = $this->auth->getUser();
        $config = get_addon_config('nft');
        $config = $config['marketing'];
        $article = \addons\nft\model\Article::where('id', $config['box'] ?? 0)->find(); //活动规则
        $level_first = Box::where('level', 1)->where('status', 'normal')->limit(3)->select();
        $level_second = Box::where('level', 2)->where('status', 'normal')->limit(3)->select();
        $level_third = Box::where('level', 3)->where('status', 'normal')->limit(3)->select();
        $list = [];
        foreach ($level_first as $item) {
            $list[] = ['name' => $item->collection->title, 'level_text' => $item->level_text, 'level' => 1, 'image' => cdnurl($item->collection->image, true)];
        }
        foreach ($level_second as $item) {
            $list[] = ['name' => $item->collection->title, 'level_text' => $item->level_text, 'level' => 2, 'image' => cdnurl($item->collection->image, true)];
        }
        foreach ($level_third as $item) {
            $list[] = ['name' => $item->collection->title, 'level_text' => $item->level_text, 'level' => 3, 'image' => cdnurl($item->collection->image, true)];
        }
        //盲盒商品
//        $list = [
//            ['name'=>'诺斯先生．轩轩','level_text'=>'普通','level'=>1,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．天天','level_text'=>'普通','level'=>1,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．麒麟','level_text'=>'普通','level'=>1,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．雯雯','level_text'=>'稀有','level'=>2,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．悠悠','level_text'=>'稀有','level'=>2,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．丰丰','level_text'=>'稀有','level'=>2,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．赫赫','level_text'=>'史诗','level'=>3,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．堪堪','level_text'=>'史诗','level'=>3,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//            ['name'=>'诺斯先生．刚刚','level_text'=>'史诗','level'=>3,'image'=>cdnurl('/uploads/20220312115349.jpg', true)],
//        ];
        $box_award = $this->get_box_rule();
        $my_award = [
            1 => UserBox::where('user_id', $this->auth->id)->where('level', 1)->where('state', 2)->count(),
            2 => UserBox::where('user_id', $this->auth->id)->where('level', 2)->where('state', 2)->count(),
            3 => UserBox::where('user_id', $this->auth->id)->where('level', 3)->where('state', 2)->count()
        ];

        $collection_id = Box::where('status', 'normal')->column('collection_id');
        $head_image = \addons\nft\model\Collection::where('id', 'in', $collection_id)->column('title');
        $compound = \addons\nft\model\Article::where('id', $config['compound'] ?? 0)->find(); //活动规则
        $box_num = 1;
        if ($this->auth->id) {
            $box_status = array_column($box_award, 'status');
            $box_num = 0;
            foreach ($box_status as $box_status) {
                if ($box_status == 1) {
                    $box_num++;
                }
            }
        }
        $this->success('获取成功', [
            'rule' => $article['content'] ?? '',
            'compound' => $compound['content'] ?? '',
            'box_list' => $list,
            'box_num' => $box_num,
            'box_award' => $box_award,
            'my_award' => $my_award,
            'head_image' => $head_image,
            'friend_num' => UserFriend::hasWhere('identify')->where('pid', $this->auth->id)->count()
        ]);
    }

    /**
     * 获取抽奖规则
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function get_box_rule()
    {
        $all_bow_award = Box::where('status', 'normal')->order('people_limit asc')->select();
        $box_award = [];
        $people_num = 0;
        $user_friend = UserFriend::hasWhere('identify')->where('pid', $this->auth->id)->count();
        foreach ($all_bow_award as $item) {
            $people_num += $item->people_limit;
            $status = 0;
            if (empty($this->auth->id) && $people_num == 1) {
                $status = 1;
            } else {
                $user_box = UserBox::where('user_id', $this->auth->id)->where('box_id', $item->id)->find();
                if (!empty($user_box)) {
                    $status = $user_box['state'];
                } else {
                    if ($user_friend > $people_num) {
                        $status = 2;
                    }
                }
            }
            $box_award[] = ['num' => $people_num, 'award' => $item->level_text . '盲盒一次', 'limit' => $item->collection->stock, 'status' => $status, 'level' => $item->level, 'id' => $item->id, 'people_limit' => $item->people_limit];
        }
        return $box_award;
    }

    public function random_box()
    {
        if (empty($this->auth->getUser()->identify)) {
            $this->error('请先完成实名制', null, 402);
        }
        if (empty($this->auth->getUser()->activity->num)) {
            $this->error('无未开启的盲盒');
        }
        Db::startTrans();
        try {
            // 开始获取盒子
            $all_bow_award = $this->get_box_rule();
            foreach ($all_bow_award as $item) {
                if ($item['status'] == 0) {
                    $collection_ids = Box::where('level', $item['level'])->column('collection_id');
                    if (!empty($collection_ids)) {
                        $key = array_rand($collection_ids, 1);
                        $collection_id = $collection_ids[$key];
                        UserBox::create([
                            'user_id' => $this->auth->id,
                            'collection_id' => $collection_id,
                            'box_id' => $item['id'],
                            'level' => $item['level']
                        ]);
                        $this->auth->getUser()->activity->setDec('num', $item['people_limit']);
                        break;
                    }
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }

        //拿取一个未开启的盲盒
        $user_box = UserBox::where('user_id', $this->auth->id)->where('state', 1)->order('id asc')->find();
        try {
            $key = 'collection_' . $user_box->collection_id;
            if (!PayService::checkoutStock($key, 1)) {
                $user_box->state = 2;
                $user_box->save();
                $this->error('未获得');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error('系统错误');
        }
        if (!UserCollection::addLog($user_box->collection_id, $this->auth->id)) {
            $this->error('系统繁忙,请稍后尝试');
        }
        $user_box->state = 2;
        $user_box->save();

        $this->success('抽取成功', [
            'image' => cdnurl($user_box->collection->image, true),
            'name' => $user_box->collection->title,
            'level_text' => $user_box->collection->level_text,
            'level' => 1
        ]);
    }

    /**
     * 领取空投
     *
     * @param null $id
     */
    public function draw_air_drop($id = null)
    {
        $info = AirDrop::where('id', $id)->where('user_id',$this->auth->id)->lock(true)->find();
        if (empty($info)) {
            $this->error('未找到相关记录');
        }
        if ($info->state != 0) {
            $this->error('空投已领取或失效');
        }
        if ($info->limit_time <= time()) {
            $info->state = 3;
            $info->save();
            $this->error('空投失效');
        }
        $stock_key = 'collection_'.$info->collection_id;
        if(!PayService::checkoutStock($stock_key,1)){
            $this->error('下次早点来,已抢空');
        }
        Db::startTrans();
        try {
            $info->state = 1;
            $info->save();
            Queue::push(NoJob::class, [
                'id' => $info->collection_id,
                'user_id' => $this->auth->id,
                'air_id'=>$id,
                'type'=>'air',
            ],'createno');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            Log::error('空投失败' . $e->getMessage());
            $this->error('操作频繁,请稍后再试');
        }
        $this->success('领取成功');
    }
}
