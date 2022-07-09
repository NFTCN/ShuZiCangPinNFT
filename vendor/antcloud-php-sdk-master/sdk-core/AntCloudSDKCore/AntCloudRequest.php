<?php

namespace AntCloudSDKCore;


/**
 * Class AntCloudRequest
 * @package AntCloudSDKCore
 */
abstract class AntCloudRequest
{
    public $method;
    public $version;
    public $productInstanceId;
    public $customer;
    public $tenant;
    public $workspace;
    public $reqMsgId;
    public $reqBizId;
    public $authToken;

    /**
     * AntCloudRequest constructor.
     * @param $method
     * @param $version
     * @param null $productInstanceId
     * @param null $customer
     * @param null $tenant
     * @param null $workspace
     * @param null $reqMsgId
     * @param null $reqBizId
     * @param null $authToken
     */
    public function __construct($method,
                                $version,
                                $productInstanceId = null,
                                $customer = null,
                                $tenant = null,
                                $workspace = null,
                                $reqMsgId = null,
                                $reqBizId = null,
                                $authToken = null)
    {
        $this->method = $method;
        $this->version = $version;
        $this->productInstanceId = $productInstanceId;
        $this->customer = $customer;
        $this->tenant = $tenant;
        $this->workspace = $workspace;
        $this->reqMsgId = $reqMsgId;
        $this->reqBizId = $reqBizId;
        $this->authToken = $authToken;
    }

    /**
     * 获取请求体参数
     * @return mixed
     */
    public function getBodyParams()
    {
        return json_decode(json_encode($this), true);
    }
}