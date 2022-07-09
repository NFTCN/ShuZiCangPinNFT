<?php

namespace AntCloudSDKCore\Http;


/**
 * Class HttpRequest
 * @package AntCloudSDKCore\Http
 */
class HttpRequest
{
    const POST_METHOD = 'POST';
    const HTTPS_SCHEME = 'https';
    const HTTP_PORT = 80;
    const HTTPS_PORT = 443;

    public $url;
    public $host;
    public $port;
    public $content;
    public $method;
    public $headers;
    public $keyFile;
    public $certFile;
    public $timeout;

    /**
     * HttpRequest constructor.
     * @param $url
     * @param $content
     * @param string $method
     * @param null $headers
     * @param null $keyFile
     * @param null $certFile
     * @param null $timeout
     */
    public function __construct($url,
                                $content,
                                $method = self::POST_METHOD,
                                $headers = null,
                                $keyFile = null,
                                $certFile = null,
                                $timeout = null)
    {
        $this->url = $url;
        $this->host = parse_url($url, PHP_URL_HOST);

        /**
         * 获取请求端口
         */
        if (parse_url($url, PHP_URL_PORT)) {
            $this->port = parse_url($url, PHP_URL_PORT);
        } else {
            if ($this->isHttps()) {
                $this->port = self::HTTPS_PORT;
            } else {
                $this->port = self::HTTP_PORT;
            }
        }

        $this->content = $content;
        $this->method = $method;
        $this->headers = $headers;
        $this->keyFile = $keyFile;
        $this->certFile = $certFile;
        $this->timeout = $timeout;
    }

    /**
     * 是否是https url地址
     * @return bool
     */
    public function isHttps()
    {
        return parse_url($this->url, PHP_URL_SCHEME) === self::HTTPS_SCHEME;
    }

    /**
     * 判断是否为Post方法
     * @return bool
     */
    public function isPost()
    {
        return $this->method === self::POST_METHOD;
    }
}