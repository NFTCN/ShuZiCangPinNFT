<?php

namespace AntCloudSDKCore\Util;


/**
 * Class CommonUtil
 * @package AntCloudSDKCore\Util
 */
class CommonUtil
{
    const SUCCESS_RESULT_CODE = 'OK';

    /**
     * 生成一个随机的req msg id，用于在日志中定位请求
     */
    public static function generateReqMsgId()
    {
        return md5(uniqid('', true));
    }

    /**
     * 获取当前的日期
     * @return false|string
     */
    public static function generateIsoFormatCurrentDate()
    {
        return date('c');
    }

    /**
     * 判断是否是成功的response
     * @param $respContent
     * @return bool
     */
    public static function isSuccessResp($respContent)
    {
        return array_key_exists('result_code', $respContent) && $respContent['result_code'] === static::SUCCESS_RESULT_CODE;
    }
}