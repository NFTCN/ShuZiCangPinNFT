<?php

namespace AntCloudSDKCore\Exception;


/**
 * 专指客户端调用时出现的异常
 * Class ClientException
 * @package AntCloudSDKCore\Exception
 */
class ClientException extends \Exception
{

    public $errCode;
    public $errMsg;

    /**
     * ClientException constructor.
     * @param $errCode
     * @param $errMsg
     */
    public function __construct($errCode, $errMsg)
    {
        $this->errCode = $errCode;
        $this->errMsg = $errMsg;
    }

    public function __toString()
    {
        return "$this->errCode, $this->errMsg";
    }
}