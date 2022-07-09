<?php

namespace addons\nft\library\job;


use addons\nft\model\AirDrop;
use addons\nft\model\UserCollection;
use app\common\service\nft\PayService;
use fast\Random;
use think\Db;
use think\queue\Job;


class AirJob
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
            $info = AirDrop::where('id', $data['air_id'])->where('user_id', $data['user_id'])->lock(true)->find();
            if (!empty($info) && $info->state == 1) {
                UserCollection::addAirLog($data['id'], $data['user_id'], $data['test_no']);
                $info->state = 2;
                $info->save();
                //生成藏品记录
            }
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