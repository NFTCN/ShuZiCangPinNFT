<?php

namespace AntCloudSDKCore\Auth;


/**
 * 签名类
 * Class Signer
 * @package AntCloudSDKCore\Auth
 */
class Signer
{
    /**
     * 获取签名
     * @param $params array|string 请求参数
     * @param $secret string accessSecret
     * @return string
     */
    public static function sign($params, $secret)
    {
        if (is_array($params)) {
            // 参数排序
            ksort($params);

            // 做标准的url encode，得到待签名的字符串
            $strToSign = '';
            foreach ($params as $key => $value) {
                $strToSign .= '&' . static::standardUrlEncode($key) . '=' . static::standardUrlEncode($value);
            }
            $strToSign = substr($strToSign, 1);
        }
        else {
            // 如果已经是字符串了，那么不用做其他的处理
            $strToSign = $params;
        }

        // 签名
        return base64_encode(hash_hmac('sha1', $strToSign, $secret, true));
    }

    /**
     * 抽取出需要签名的response字符串
     * response中的固定格式为：{"response": RESPONSE_JSON, "sign": SIGN_STRING}
     * 其中RESPONSE_JSON为需要签名的字段，SIGN_STRING为产品返回的签名
     * @param $respBody string 原始的response内容
     * @return bool|null|string
     */
    public static function extractRespStrToSign($respBody)
    {
        // 先看一下通过decode再encode，判断是否刚好是原始respBody的子串的，是的话直接返回
        $respDecoded = json_decode($respBody, true);
        $respContent = $respDecoded['response'];
        $respContentJsonStr = json_encode($respContent);
        if (strpos($respBody, $respContentJsonStr) !== false) {
            return $respContentJsonStr;
        }

        // 如果不是的话（比如因为respBody中带有一些不必要的空格换行等字符），我们需要自己手动解析出来
        // 首先判断response和sign在json string中哪个排在前面
        $respNodeKey = '"response"';
        $signNodeKey = '"sign"';
        $respFirstOccurIdx = strpos($respBody, $respNodeKey);
        $signFirstOccurIdx = strpos($respBody, $signNodeKey);
        if ($respFirstOccurIdx === false || $signFirstOccurIdx === false) {
            // 没有response或者sign直接返回空
            return null;
        }
        $respNodeStartIdx = $respFirstOccurIdx;
        $extractStartIdx = strpos($respBody, '{', $respNodeStartIdx);
        if ($respFirstOccurIdx < $signFirstOccurIdx) {
            // response出现在sign前面
            $signLastOccurIdx = strrpos($respBody, $signNodeKey);
            $extractEndIdx = strrpos($respBody, '}', $signLastOccurIdx - strlen($respBody));
        }
        else {
            // response出现在sign后面
            $extractEndIdx = strrpos($respBody, '}',-2);
        }
        return substr($respBody, $extractStartIdx, $extractEndIdx + 1 - $extractStartIdx);
    }

    /**
     * 返回标准的url encode之后的字符串
     * @param $str string 待encode的字符串
     * @return null|string|string[]
     */
    private static function standardUrlEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}

