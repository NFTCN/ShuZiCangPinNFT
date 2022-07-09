<?php

namespace AntCloudSDKCore;

use AntCloudSDKCore\Auth\Signer;
use AntCloudSDKCore\Exception\ClientException;
use AntCloudSDKCore\Http\HttpRequest;
use AntCloudSDKCore\Http\WebUtil;
use AntCloudSDKCore\Util\CommonUtil;


/**
 * 访问Open API的Client类
 * Class AntCloudClient
 * @package AntCloudSDKCore
 */
class AntCloudClient
{
    const DEFAULT_SIGN_TYPE = 'HmacSHA1';

    public $endpoint;
    public $accessKey;
    public $accessSecret;
    public $checkSign;
    public $timeout;
    public $userAgent;
    public $debugMode;

    /**
     * AntCloudClient constructor.
     * @param $endpoint string 访问地址
     * @param $accessKey string
     * @param $accessSecret string
     * @param bool $checkSign 是否需要校验签名
     * @param null $timeout 超时时间
     * @param null $userAgent
     * @param bool $debugMode 调试模式，调试模式下，会输出较多的日志信息
     */
    public function __construct($endpoint,
                                $accessKey,
                                $accessSecret,
                                $checkSign = true,
                                $timeout = null,
                                $userAgent = null,
                                $debugMode = false)
    {
        $this->endpoint = $endpoint;
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
        $this->checkSign = $checkSign;
        $this->timeout = $timeout;
        $this->userAgent = $userAgent;
        $this->debugMode = $debugMode;
    }

    /**
     * 处理api调用请求
     * @param $request
     * @param bool $returnRawResp
     * @return mixed
     * @throws ClientException
     */
    public function execute($request, $returnRawResp = false)
    {
        // 判断request变量的类型
        if (!is_array($request) && !$request instanceof AntCloudRequest) {
            throw new \InvalidArgumentException('The type of request should be array or the subclass of AntCloudRequest');
        }

        // 构造request最后发送的content
        $content = $this->buildContent($request);

        // 校验请求参数
        $this->validateParams($content);

        // 构造http request
        $httpRequest = new HttpRequest($this->endpoint, $content);

        // 获取请求响应response
        $httpResponse = WebUtil::getResponse($httpRequest);

        // 解析response
        if ($returnRawResp) {
            return $httpResponse->body;
        }
        $respDecoded = json_decode($httpResponse->body, true);
        if ($respDecoded === null || !array_key_exists('response', $respDecoded) || $respDecoded['response'] === null) {
            throw new ClientException(ANTCLOUD_SDK_TRANSPORT_ERROR, "Unexpected gateway response: $httpResponse->body");
        }
        $respContent = $respDecoded['response'];
        if (CommonUtil::isSuccessResp($respContent) && $this->checkSign) {
            // 校验response的签名
            if (!array_key_exists('sign', $respDecoded)) {
                throw new ClientException(ANTCLOUD_SDK_BAD_SIGNATURE, 'Invalid signature in response');
            }
            $signature = $respDecoded['sign'];
            $strToSign = Signer::extractRespStrToSign($httpResponse->body);
            if (Signer::sign($strToSign, $this->accessSecret) !== $signature) {
                throw new ClientException(ANTCLOUD_SDK_BAD_SIGNATURE, 'Invalid signature in response');
            }
        }

        return $respContent;
    }

    /**
     * 根据传入的DTO数据，构造request的请求content
     * @param $request
     * @return array
     */
    private function buildContent($request)
    {
        if ($request instanceof AntCloudRequest) {
            $requestParams = $request->getBodyParams();
        } else {
            $requestParams = $request;
        }

        $content = WebUtil::buildCustomFormParams($requestParams);
        $content['access_key'] = $this->accessKey;
        $content['sign_type'] = self::DEFAULT_SIGN_TYPE;
        $content['req_msg_id'] = CommonUtil::generateReqMsgId();
        $content['req_time'] = CommonUtil::generateIsoFormatCurrentDate();
        $content['sdk_version'] = ANTCLOUD_SDK_VERSION;
        $content['sign'] = Signer::sign($content, $this->accessSecret);
        return $content;
    }

    /**
     * 校验request的content是否合法
     * @param $content
     */
    private function validateParams($content)
    {
        if ($content['method'] === null) {
            throw new \InvalidArgumentException("Request method can't be null");
        }
        if ($content['version'] === null) {
            throw new \InvalidArgumentException("Request version can't be null");
        }
    }
}