<?php
namespace AntCloudSDKCore\Http;


/**
 * Class HttpResponse
 * @package AntCloudSDKCore\Http
 */
class HttpResponse
{
    public $status_code;
    public $body;

    /**
     * HttpResponse constructor.
     * @param $status_code
     * @param $body
     */
    public function __construct($status_code, $body)
    {
        $this->status_code = $status_code;
        $this->body = $body;
    }
}