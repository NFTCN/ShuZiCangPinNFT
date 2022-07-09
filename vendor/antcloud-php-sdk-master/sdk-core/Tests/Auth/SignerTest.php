<?php

namespace Tests\Auth;

require_once '../../AntCloudSDKCore/Config.php';

use AntCloudSDKCore\Auth\Signer;
use PHPUnit\Framework\TestCase;

class SignerTest extends TestCase
{

    public function testSign()
    {
        $params = array(
            'method' => 'antcloud.acm.tenant.get',
            'req_msg_id' => 'c60a76d67f57431c89d3d046e7f84a40',
            'access_key' => 'LTAIyqaeoWfELqMg',
            'version' => '1.0',
            'sign_type' => 'HmacSHA1',
            'tenant' => 'tenant',
            'req_time' => '2018-03-21T03:41:59Z'
        );
        $secret = 'BXXb9KtxtWtoOGui88kcu0m6h6crjW';
        $result = Signer::sign($params, $secret);
        $expected = '0MJMBmupGPBF1EHokaBF9cmmMuw=';
        $this->assertEquals($expected, $result);
    }

    public function testExtractRespStrToSign()
    {
        $strIn1 = <<<'STR'
{"response":{"a":1,
            "c":"hello"}, "sign":"abcde"}
STR;
        $strIn2 = <<<'STR'
{"sign":"abcde", "response":{"a":1,
            "c":"hello"}}
STR;
        $strOut = <<<'STR'
{"a":1,
            "c":"hello"}
STR;


        $result1 = Signer::extractRespStrToSign('{"response":{"a":1,"c":"hello"}, "sign":"abcde"}'); // 标准格式
        $result2 = Signer::extractRespStrToSign('{"response":{"a":1,    "c":"hello"}, "sign":"abcde"}'); // response中带较多空格
        $result3 = Signer::extractRespStrToSign($strIn1); // response中带回车
        $result4 = Signer::extractRespStrToSign('{"sign":"abcde", "response":{"a":1,"c":"hello"}}'); // response和sign换个位置
        $result5 = Signer::extractRespStrToSign('{"sign":"abcde", "response":{"a":1,    "c":"hello"}}'); // response中带较多空格
        $result6 = Signer::extractRespStrToSign($strIn2); // response中带回车
        $this->assertEquals('{"a":1,"c":"hello"}', $result1);
        $this->assertEquals('{"a":1,    "c":"hello"}', $result2);
        $this->assertEquals($strOut, $result3);
        $this->assertEquals('{"a":1,"c":"hello"}', $result4);
        $this->assertEquals('{"a":1,    "c":"hello"}', $result5);
        $this->assertEquals($strOut, $result6);
    }
}
