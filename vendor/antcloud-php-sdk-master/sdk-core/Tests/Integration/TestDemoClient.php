<?php
namespace Tests\Integration;

require_once '../../AntCloudSDKCore/Config.php';

use AntCloudSDKCore\AntCloudClient;
use PHPUnit\Framework\TestCase;

class TestDemoClient extends TestCase
{

    public function testEchoGatewayCheck()
    {
        $endpoint = 'http://apigw.dev.pub.jr.alipay.net/gateway.do';
        $accessKey = 'LTAIyqaeoWfELqMg';
        $accessSecret = 'BXXb9KtxtWtoOGui88kcu0m6h6crjW';
        $request = array(
            'method' => 'antcloud.demo.gateway.check.echo',
            'version' => '1.0',
            'input_string' => 'hello world'
        );
        $client = new AntCloudClient($endpoint, $accessKey, $accessSecret);
        $response = $client->execute($request);
        $this->assertEquals($request['input_string'], $response['output_string']);
    }

}