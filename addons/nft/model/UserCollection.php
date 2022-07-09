<?php

namespace addons\nft\model;

use app\common\service\nft\NftService;
use fast\Random;
use think\Db;
use think\Model;
use traits\model\SoftDelete;

class UserCollection extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'nft_user_collection';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];


    /**
     * 初始交易记录(抽取)
     *
     * @param $collection_id
     * @param $user_id
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addLog($collection_id, $user_id)
    {
        /**
         *
         */
        $user = User::where('id', $user_id)->find();
        $collection = Collection::where('id', $collection_id)->find();
        $market = self::where('collection_id', $collection_id)->count();
        if($market>= $collection->stock){
            return false;
        }
        Db::startTrans();
        try {
            //创建链信息
            $server = NftService::instance();
            $salt = Random::alpha(6);
            $no = ($market + 1) . '/' . $collection->stock;
            $server->createTokenId($no, $collection->author->name, $collection->image,$salt);

            //创建第一次的交易记录
            $server->createHash($collection->issuer->name);
            //创建第二次的交易记录
            $pay_hash = $server->createHash($user->nickname)->getHash();
            self::create([
                'user_id' => $user_id,
                'title' => $collection->title,
                'image' => $collection->image,
                'owner' => $user->nickname,
                'tokenId' => $server->getTokenId(),
                'author' => $collection->author->name,
                'level' => $collection->level ?? 0,
                'hash_no' => $pay_hash,
                'no' => $no,
                'collection_id' => $collection_id
            ]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }


    /**
     * 初始交易记录(无事务)
     *
     * @param $collection_id
     * @param $user_id
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addPayLog($collection_id, $user_id,$test_no)
    {
        $user = User::where('id', $user_id)->find();
        $collection = Collection::where('id', $collection_id)->find();
        //创建链信息
        $server = NftService::instance();
        $salt = Random::alpha(6);
        $no = $test_no . '/' . $collection->stock;
        $server->createTokenId($no, $collection->author->name, $collection->image, $salt);

        //创建第一次的交易记录
        $server->createHash($collection->issuer->name);
        //创建第二次的交易记录
        $pay_hash = $server->createHash($user->nickname)->getHash();
        self::create([
            'user_id' => $user_id,
            'title' => $collection->title,
            'image' => $collection->image,
            'owner' => $user->nickname,
            'tokenId' => $server->getTokenId(),
            'author' => $collection->author->name,
            'level' => $collection->level ?? 0,
            'hash_no' => $pay_hash,
            'no' => $no,
            'collection_id' => $collection_id,
            'salt' => $salt

        ]);
    }


    /**
     * 空投交易
     *
     * @param $collection_id
     * @param $user_id
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addAirLog($collection_id, $user_id,$test_no)
    {
        /**
         *
         */
        $user = User::where('id', $user_id)->find();
        $collection = Collection::where('id', $collection_id)->find();
        $market = self::where('collection_id', $collection_id)->count();
        //创建链信息
        $server = NftService::instance();

        $salt = Random::alpha(6);
        $no = $test_no . '/' . $collection->stock;
        $server->createTokenId($no, $collection->author->name, $collection->image, $salt);

        //创建第一次的交易记录
        $server->createHash($collection->issuer->name);
        //创建第二次的交易记录
        $pay_hash = $server->createHash($user->nickname)->getHash();
        self::create([
            'user_id' => $user_id,
            'title' => $collection->title,
            'image' => $collection->image,
            'owner' => $user->nickname,
            'tokenId' => $server->getTokenId(),
            'author' => $collection->author->name,
            'level' => $collection->level ?? 0,
            'hash_no' => $pay_hash,
            'no' => $no,
            'collection_id' => $collection_id,
            'salt' => $salt
        ]);
    }

    /**
     * 发放纹章
     * @param $collection_id
     * @param $user_id
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addBadgeLog($collection_id, $user_id,$test_no)
    {
        /**
         *
         */
        $user = User::where('id', $user_id)->find();
        $collection = Collection::where('id', $collection_id)->find();
        $salt = Random::alpha(6);
        //创建链信息
        $server = NftService::instance();
        $no = $test_no . '/' . $collection->stock;
        $server->createTokenId($no, $collection->author->name, $collection->image, $salt);
        //创建第一次的交易记录
        $server->createHash($collection->issuer->name);
        //创建第二次的交易记录
        $pay_hash = $server->createHash($user->nickname)->getHash();
        self::create([
            'user_id' => $user_id,
            'title' => $collection->title,
            'image' => $collection->image,
            'owner' => $user->nickname,
            'tokenId' => $server->getTokenId(),
            'author' => $collection->author->name,
            'level' => $collection->level ?? 0,
            'hash_no' => $pay_hash,
            'no' => $no,
            'collection_id' => $collection_id,
            'salt' => $salt

        ]);
        return $server->getTokenId();
    }



    public static function addGiveLog($tokenId, $user_id)
    {
        Db::startTrans();
        try {
            $user = User::where('id', $user_id)->find();
            //获取藏品信息
            $user_collection = self::where('tokenId', $tokenId)->find();
            $server = NftService::instance();
            //设置上一次的hash值
            $ret = $server->createTokenId($user_collection->no,$user_collection->author,$user_collection->image,$user_collection->salt?:'')
                ->setHash($user_collection->hash_no);
            if (!$ret) {
                //哈希值错误
                return false;
            }
            $hash = $server->createHash($user->nickname)->getHash();
            //记录赠与记录
            UserCollectionGiveLog::create([
                'tokenId' => $server->getTokenId(),
                'user_id' => $user_collection->user_id,
                'owner' => $user->nickname,
                'hash_no' => $hash,
            ]);
            $user_collection->user_id = $user_id;
            $user_collection->hash_no = $hash;
            $user_collection->owner = $user->nickname;
            $user_collection->save();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }


    public function log()
    {
        return $this->hasOne(UserCollectionLog::class, 'tokenId', 'tokenId');
    }

    public function logs()
    {
        return $this->hasMany(UserCollectionLog::class, 'tokenId', 'tokenId');
    }


}
