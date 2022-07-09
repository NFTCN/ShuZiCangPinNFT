<?php

namespace AntCloudSDKCore\Http;

use AntCloudSDKCore\Exception\ClientException;

class WebUtil
{
    /**
     * 构建标准的表单参数
     * @param $bodyParams
     * @return array
     */
    public static function buildCustomFormParams($bodyParams)
    {
        if (!is_array($bodyParams)) {
            throw new \InvalidArgumentException('Body params should be array');
        }

        // 将post body按照约定的格式转换成form表单参数
        // 对于嵌套的结构，需要递归转换
        $ret = array();
        static::build($ret, '', $bodyParams);
        return $ret;
    }

    /**
     * 递归构建form表单参数
     * @param $result
     * @param $path
     * @param $param
     */
    private static function build(&$result, $path, $param)
    {
        if (is_array($param)) {
            if (static::isAssoc($param)) {
                foreach ($param as $key => $value) {
                    static::build($result, $path . '.' . $key, $value);
                }
            } else {
                foreach ($param as $key => $value) {
                    static::build($result, $path . '.' . ($key + 1), $value);
                }
            }
        } else {
            if ($param !== null) {
                $result[substr($path, 1)] = $param;
            }
        }
    }

    /**
     * 判断数组是否为associate数组（即map），还是List
     * @param $arr
     * @return bool
     */
    private static function isAssoc($arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * 获取请求的响应结果
     * @param $request HttpRequest
     * @return HttpResponse
     * @throws ClientException
     */
    public static function getResponse($request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($request->isPost()) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, static::buildFinalRequestContent($request->content));
        } else if ($request->method) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->method);
        }
        if ($request->timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $request->timeout);
        }

        // https request
        if ($request->isHttps()) {
            // 防止HTTPS连接出现异常
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        // set headers
        if (is_array($request->headers) && !empty($request->headers)) {
            $httpHeaders = static::buildFinalHttpHeaders($request->headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }

        // get response
        $body = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpResponse = new HttpResponse($status_code, $body);
        if (curl_errno($ch)) {
            throw new ClientException(ANTCLOUD_SDK_TRANSPORT_ERROR, "Server connect error, curl error number:" . curl_errno($ch) . " message:" . curl_error($ch));
        }
        curl_close($ch);
        return $httpResponse;
    }

    /**
     * 构建最后的提交参数
     * @param $postFields
     * @return bool|string
     */
    private static function buildFinalRequestContent($postFields)
    {
        if (is_string($postFields)) {
            return $postFields;
        }
        $content = '';
        foreach ($postFields as $fieldName => $apiParamValue) {
            $content .= "$fieldName=" . urlencode($apiParamValue) . '&';
        }
        return substr($content, 0, -1);
    }

    /**
     * 构建最后的header设置值
     * @param $headers
     * @return array
     */
    private static function buildFinalHttpHeaders($headers)
    {
        $httpHeader = array();
        foreach ($headers as $key => $value) {
            $httpHeader[] = $key . ':' . $value;
        }
        return $httpHeader;
    }

}

