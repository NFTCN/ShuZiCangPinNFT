<?php
namespace Tests\Util;

require_once '../../AntCloudSDKCore/Config.php';

use AntCloudSDKCore\Util\CommonUtil;
use PHPUnit\Framework\TestCase;

class CommonUtilTest extends TestCase
{

    public function testGenerateReqMsgId()
    {
        $this->assertTrue(strlen(CommonUtil::generateReqMsgId()) === 32);
    }

    public function testIsSuccessResp()
    {
        $this->assertTrue(CommonUtil::isSuccessResp(array('result_code' => 'OK', 'a' => 1)));
        $this->assertFalse(CommonUtil::isSuccessResp(array('a' => 1)));
    }
}
