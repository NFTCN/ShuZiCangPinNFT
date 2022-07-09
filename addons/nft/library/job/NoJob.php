<?php

namespace addons\nft\library\job;


use addons\nft\model\AirDrop;
use addons\nft\model\Collection;
use addons\nft\model\UserCollection;
use app\common\service\nft\PayService;
use fast\Random;
use think\Cache;
use think\Db;
use think\Queue;
use think\queue\Job;


class NoJob
{

    public function fire(Job $job, $data)
    {
        echo '开始任务分发 参数' . json_encode($data);

        $stock_key = 'collection_' . $data['id'];

        //开始生成编号
        if (Cache::has($stock_key . 'no')) {
            $test_no = Cache::inc($stock_key . 'no');
        } else {
            $market = UserCollection::where('collection_id', $data['id'])->count();
            $test_no = bcadd($market, 1);
            Cache::set($stock_key . 'no', $test_no);
        }
        if (empty($test_no)) {
            $job->release();
            return false;
        }
        switch ($data['type']) {
            case 'air':
                if (!PayService::checkoutStock($stock_key, 1)) {
                    $job->delete();
                    echo '藏品超发' . $stock_key;
                    return true;
                }
                Queue::push(AirJob::class, [
                    'id' => $data['id'],
                    'user_id' => $data['user_id'],
                    'air_id' => $data['air_id'],
                    'test_no' => $test_no
                ]);
                PayService::subStock($stock_key, 1);

                break;
            case 'pay':
                Queue::push(CollectionJob::class, [
                    'id' => $data['id'],
                    'market' => 1,
                    'user_id' => $data['user_id'],
                    'order_id' => $data['order_id'],
                    'test_no' => $test_no
                ]);
                break;
            default:
                $job->delete();
                echo '无效任务';
                return false;
        }

        echo '分发完成 类型' . $data['type'] . '  编号:' . $test_no;
        $job->delete();
    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }

}