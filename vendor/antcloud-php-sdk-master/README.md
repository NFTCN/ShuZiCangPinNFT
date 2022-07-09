欢迎使用金融云PHP SDK。

## SDK使用

1. 创建Client实例并配置对应参数。
2. 创建Request DTO类，填充请求参数。或者直接构造字典，如果使用字典参数，则需要手动地往字典中设置method和version的值。
3. 调用Client类的execute方法，获取响应结果。


### 通过Array传递请求参数：

```php
<?php

require_once './antcloud-php-sdk/sdk-core/AntCloudSDKCore/Config.php';

use AntCloudSDKCore\AntCloudClient;

// 创建 Client 实例
$client = new AntCloudClient(
   "<endpoint>",
   "<your-access-key>",
   "<your-access-secret>"
);

// 创建 Request，并填充请求参数
// 如果通过array的方式传递参数，method、version是必须设置的参数
// 如果访问的是非核心网关（即产品网关），那么product_instance_id也是必须设置的参数
$request = array(
    'method' => 'antcloud.demo.gateway.check.echo',
    'version' => '1.0',
    'input_string' => 'hello world',
);

// 发送调用请求，解析响应结果
$response = $client->execute($request);
var_dump($response);

```
