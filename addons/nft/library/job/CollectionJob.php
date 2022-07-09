<?php

namespace addons\nft\library\job;


use addons\nft\model\Collection;
use addons\nft\model\Order;
use addons\nft\model\OrderGoods;
use addons\nft\model\UserCollection;
use fast\Random;
use think\Db;
use think\queue\Job;


class CollectionJob
{

    public function fire(Job $job, $data)
    {
        echo '开始任务';
        if(empty($data['test_no'])){
            //缺少编号 无效任务
            $job->delete();
            return false;
        }
        //....这里执行具体的任务
        Db::startTrans();
        try {
            Collection::where(['id' => $data['id']])->setInc('market', $data['market']);
            //开始写入日志
            UserCollection::addPayLog($data['id'], $data['user_id'], $data['test_no']);
            //更改订单商品链状态
            OrderGoods::where(['order_id' => $data['order_id'], 'goods_id' => $data['id']])->update(['goods_status' => 1]);
            //订单所有的藏品链状态已经完成
            $order = Order::get($data['order_id']);
            $order->order_status = 2;
            $order->save();
//            if (OrderGoods::where(['order_id' => $data['order_id'], 'goods_status' => 0])->count() == 0) {
//
//            }
            Db::commit();
            $job->delete();
            echo '任务结束';
        } catch (\Exception $e) {
            echo '任务出错' . $e->getMessage();
            Db::rollback();
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
            } else {
                // 也可以重新发布这个任务
                $job->release(); //$delay为延迟时间
            }
        }


    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }

}