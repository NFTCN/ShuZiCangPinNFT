<?php

namespace addons\nft\model;

use addons\nft\library\Common;
use think\Db;
use think\Model;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * 会员模型
 * @property int|null $id 会员id
 */
class User extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'url',
    ];

    /**
     * 获取个人URL
     * @param string $value
     * @param array  $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        return "/u/" . $data['id'];
    }

    /**
     * 获取头像
     * @param string $value
     * @param array  $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        if (!$value) {
            //如果不需要启用首字母头像，请使用
            $value = cdnurl('assets/img/avatar.png',true);
//            $value = letter_avatar($data['nickname']);
        }
        return $value;
    }

    /**
     * 获取会员的组别
     */
    public function getGroupAttr($value, $data)
    {
        return UserGroup::get($data['group_id']);
    }

    /**
     * 获取验证字段数组值
     * @param string $value
     * @param array  $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array)json_decode($value, true));
        $value = array_merge(['email' => 0, 'mobile' => 0,'password'=>0], $value);
        return (object)$value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 变更会员余额
     * @param int    $money   余额
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     */
    public static function money($money, $user_id, $memo)
    {
        Db::startTrans();
        try {
            $user = self::lock(true)->find($user_id);
            if ($user && $money != 0) {
                $before = $user->money;
                //$after = $user->money + $money;
                $after = function_exists('bcadd') ? bcadd($user->money, $money, 2) : $user->money + $money;
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => $memo]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 变更会员积分
     * @param int    $score   积分
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     */
    public static function score($score, $user_id, $memo)
    {
        Db::startTrans();
        try {
            $user = self::lock(true)->find($user_id);
            if ($user && $score != 0) {
                $before = $user->score;
                $after = $user->score + $score;
                $level = self::nextlevel($after);
                //更新会员信息
                $user->save(['score' => $after, 'level' => $level]);
                //写入日志
                ScoreLog::create(['user_id' => $user_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 根据积分获取等级
     * @param int $score 积分
     * @return int
     */
    public static function nextlevel($score = 0)
    {
        $lv = array(1 => 0, 2 => 30, 3 => 100, 4 => 500, 5 => 1000, 6 => 2000, 7 => 3000, 8 => 5000, 9 => 8000, 10 => 10000);
        $level = 1;
        foreach ($lv as $key => $value) {
            if ($score >= $value) {
                $level = $key;
            }
        }
        return $level;
    }

    public function identify(): HasOne
    {
        return $this->hasOne(Identify::class,'user_id','id');
    }

    /**
     * 盲盒活动次数
     * @return HasOne
     */
    public function activity(): HasOne
    {
        return $this->hasOne(UserActivity::class,'user_id','id')->where('type','box');
    }

    /**
     * 盲盒活动次数
     * @return HasOne
     */
    public function activitybadge(): HasOne
    {
        return $this->hasOne(UserActivity::class,'user_id','id')->where('type','badge');
    }

    public function message(): HasMany
    {
        return $this->hasMany(UserMessage::class,'user_id','id');
    }

    public static function getUserInfo($user_id): ?User
    {
        $user = self::get($user_id);
        $info = $user;
        if(!empty($user->identify)){
            $info['identify_status'] = 1;
            $info['user_md5_link'] = $user->identify->link_md5;
        }else{
            $info['identify_status'] = 0;
            $info['user_md5_link'] = '实名认证后可自动生成';
        }
        $info['message_status'] = $user->message()->where('is_view',0)->count();
        //会员徽章数 找最高等级的徽章
        $nft_config = get_addon_config('nft');
        $badge_id = Collection::where('type','badge')->column('id');
        $list = UserCollection::where('collection_id','in',$badge_id)->where('user_id',$user->id)->group('collection_id')->field('collection_id,image')->select();
        $badge_list = [];
        $top_badge = '';
        $top = false;
        foreach ($list as $item) {
            if(!$top){
                $top_badge = cdnurl($item->image,true);
                if($item->collection_id == ($nft_config['marketing']['up_badge_id']??0)){
                    $top = true;
                }
            }
        }
        $badge_list[] = cdnurl($top_badge,true);
        $info['badge_label'] = $badge_list;
        return $info;
    }

    public function getSubscription($id)
    {
        $redis = Common::redis();
        return $redis->sIsMember('subscription'.$this->id, $id);
    }

    /**
     * 查看收藏
     * @param $id
     *
     * @return bool
     * @throws \think\Exception
     */
    public function getCollectionAuthor($id)
    {
        $redis = Common::redis();
        return $redis->sIsMember('CollectionAuthor_'.$this->id, $id);
    }

    /**
     * 增加收藏
     * @param $id
     *
     * @return bool
     * @throws \think\Exception
     */
    public function addCollectionAuthor($id)
    {
        $redis = Common::redis();
        $redis->sAdd('CollectionAuthor_' . $this->id, $id);
        return true;
    }

    /**
     * 取消收藏
     * @param $id
     *
     * @return bool
     * @throws \think\Exception
     */
    public function sRemCollectionAuthor($id)
    {
        $redis = Common::redis();
        $redis->sRem('CollectionAuthor_' . $this->id, $id);
        return true;
    }

    public function getLikeAuthor($id)
    {
        $redis = Common::redis();
        return $redis->sIsMember('LikeAuthor_'.$this->id, $id);
    }

    /**
     * 增加收藏
     * @param $id
     *
     * @return bool
     * @throws \think\Exception
     */
    public function addLikeAuthor($id)
    {
        $redis = Common::redis();
        $redis->sAdd('LikeAuthor_' . $this->id, $id);
        return true;
    }

    /**
     * 取消收藏
     * @param $id
     *
     * @return bool
     * @throws \think\Exception
     */
    public function sRemLikeAuthor($id)
    {
        $redis = Common::redis();
        $redis->sRem('LikeAuthor_' . $this->id, $id);
        return true;
    }
}
