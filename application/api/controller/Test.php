<?php

namespace app\api\controller;

use addons\nft\library\job\NoJob;
use addons\nft\model\UserCollection;
use addons\nft\model\UserCollectionLog;
use app\common\controller\Api;
use app\common\service\nft\PayService;
use think\Queue;

/**
 * 首页接口
 */
class Test extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
//        $url="http://211.149.137.53:8045/Port/default.ashx?method=SendSms&username=wangxun&password=123456&phonelist=13266813924&msg=【网讯科技】本次验证码为：3321&SendDatetime=";
        $url = "http://211.149.137.53:8045/Port/default.ashx";
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        $post_data = array(
            "method" => "SendSms",
            "username" => "wangxun",
            "password" => "123456",
            "phonelist" => "15256268050",
            "msg" => "【网讯科技】本次验证码为：3321",
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        print_r($data);
    }
}
