<?php

namespace app\api\controller\nft;
require_once __DIR__ . '/../../../../vendor/antcloud-php-sdk-master/sdk-core/AntCloudSDKCore/Config.php';

use AntCloudSDKCore\Exception\ClientException;
use app\common\controller\NftApi;
use AntCloudSDKCore\AntCloudClient;

/**
 * Created by qianxun
 * Author : Jensen
 * Date : 2022/3/29
 * Time : 下午2:56
 */
class Ants extends NftApi
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    static $table = 'collects';
    static $url = 'http://127.0.0.1:8081';
    static $Token = '123';

    public function insertA()
    {
//        $res = self::insertAnts('1', 'Jensen1', '皮卡丘2D图片', '2D图片', date('Y-m-d H:i:s'));
//        var_dump(md5('Jensen'));exit;
        //7e0e7ac2a199e8e2ecb30499a3b2153c
//        $res = self::Mint('0x926b997959aed065cdf643b54ffca16298f8fff4', 1);
//        $res = self::TransferFrom('0x789a5F01e5dD730DCA2B106b35Ce39CB621b62a2', '0x926b997959aed065cdf643b54ffca16298f8fff4', 1);
//        $res = self::Burn(1);
//        var_dump($res);
        exit;
    }

    /**
     * @Explain : Bsc生成账户地址
     * @return bool|mixed
     * @Date : 2022/4/5 下午7:18
     * @Author : By Jensen
     */
    public static function CreateAddress()
    {
        $url = self::$url . '/bsc/account/create';
        $data = self::toCurl($url);
        $data = json_decode($data, true);
        if ($data['code'] != 200) {
            return false;
        }
        return $data['data']['account'];
    }

    /*array(4)
    {
    ["keyseed"] => string(87) "[cupboard, song, runway, treat, trash, evoke, tray, recipe, gauge, kiwi, crowd, mammal]"
    ["publicKey"] => string(128) "21584c6ca49dae1a6dfaad8882d480bb70bc3349d235b8fdae4019a3ee179a36ae0f889ba6bdf4521303c3cb4237c85310943ad6daae8caa1d9f95c62975239f"
    ["privateKey"] => string(63) "96dca628c957027cee82950c1782d886e0fa0376fd44e1a5d9c063c16ea19fb"
    ["address"] => string(42) "0x926b997959aed065cdf643b54ffca16298f8fff4"
    }*/

    /*
     * array(3) {
  ["code"]=>
  int(200)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(1) {
      ["hash"]=>
      string(66) "0x13a5975036fd96b57e77771d5f3e47bcc375e78a520a9d07f54ace02afacd6aa"
  }
}
     *
     * */

    /**
     * @Explain :Bsc铸造方法，上链
     * @param $address
     * @param $OnlyToken
     * @return mixed
     * @Date : 2022/4/6 下午11:03
     * @Author : By Jensen
     */
    public static function Mint($address, $OnlyToken)
    {
        $url = self::$url . '/bsc/nft/mint';
        $param = ['address' => $address, 'tokenId' => $OnlyToken];
        $data = self::toCurl($url, $param);
        $data = json_decode($data, true);
        return $data;
    }

    /*
     * array(3) {
  ["code"]=>
  int(200)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(1) {
    ["transactionReceipt"]=>
    array(19) {
      ["transactionHash"]=>
      string(66) "0xf589f109e8bf9b4e0ed5b5e117a95d91ffa122ec252a0d01101af25b74d15ecd"
      ["transactionIndex"]=>
      int(202)
      ["blockHash"]=>
      string(66) "0x946577279c7cb9175cef5237a010cff8386eca268e27c0d5210206643103a702"
      ["blockNumber"]=>
      int(16679840)
      ["cumulativeGasUsed"]=>
      int(25000604)
      ["gasUsed"]=>
      int(63741)
      ["contractAddress"]=>
      NULL
      ["root"]=>
      NULL
      ["status"]=>
      string(3) "0x1"
      ["from"]=>
      string(42) "0x664ee44c8145ab2bdef94fcf458ef37cbead8879"
      ["to"]=>
      string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
      ["logs"]=>
      array(2) {
        [0]=>
        array(13) {
          ["removed"]=>
          bool(false)
          ["logIndex"]=>
          int(704)
          ["transactionIndex"]=>
          int(202)
          ["transactionHash"]=>
          string(66) "0xf589f109e8bf9b4e0ed5b5e117a95d91ffa122ec252a0d01101af25b74d15ecd"
          ["blockHash"]=>
          string(66) "0x946577279c7cb9175cef5237a010cff8386eca268e27c0d5210206643103a702"
          ["blockNumber"]=>
          int(16679840)
          ["address"]=>
          string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
          ["data"]=>
          string(2) "0x"
          ["type"]=>
          NULL
          ["topics"]=>
          array(4) {
            [0]=>
            string(66) "0x8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925"
            [1]=>
            string(66) "0x000000000000000000000000789a5f01e5dd730dca2b106b35ce39cb621b62a2"
            [2]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000000"
            [3]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000001"
          }
          ["transactionIndexRaw"]=>
          string(4) "0xca"
          ["blockNumberRaw"]=>
          string(8) "0xfe83a0"
          ["logIndexRaw"]=>
          string(5) "0x2c0"
        }
        [1]=>
        array(13) {
          ["removed"]=>
          bool(false)
          ["logIndex"]=>
          int(705)
          ["transactionIndex"]=>
          int(202)
          ["transactionHash"]=>
          string(66) "0xf589f109e8bf9b4e0ed5b5e117a95d91ffa122ec252a0d01101af25b74d15ecd"
          ["blockHash"]=>
          string(66) "0x946577279c7cb9175cef5237a010cff8386eca268e27c0d5210206643103a702"
          ["blockNumber"]=>
          int(16679840)
          ["address"]=>
          string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
          ["data"]=>
          string(2) "0x"
          ["type"]=>
          NULL
          ["topics"]=>
          array(4) {
            [0]=>
            string(66) "0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef"
            [1]=>
            string(66) "0x000000000000000000000000789a5f01e5dd730dca2b106b35ce39cb621b62a2"
            [2]=>
            string(66) "0x000000000000000000000000926b997959aed065cdf643b54ffca16298f8fff4"
            [3]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000001"
          }
          ["transactionIndexRaw"]=>
          string(4) "0xca"
          ["blockNumberRaw"]=>
          string(8) "0xfe83a0"
          ["logIndexRaw"]=>
          string(5) "0x2c1"
        }
      }
      ["logsBloom"]=>
      string(514) "0x00000000000000000000000000000000000000000000000040000000000000400000000200000000000000000020000000000000000000000000000000240000000000000100000000000008000000000000010000040000000000000000000000000000020000000000000000000800000000000010000000000010000000000000000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000060000010000000000000000000000000000000000000004000002000000000000000"
      ["revertReason"]=>
      NULL
      ["transactionIndexRaw"]=>
      string(4) "0xca"
      ["blockNumberRaw"]=>
      string(8) "0xfe83a0"
      ["cumulativeGasUsedRaw"]=>
      string(9) "0x17d7a9c"
      ["gasUsedRaw"]=>
      string(6) "0xf8fd"
      ["statusOK"]=>
      bool(true)
    }
  }
}
     * */

    /**
     * @Explain : 转移方法
     * @param $fromAddress
     * @param $toAddress
     * @param $OnlyToken
     * @return mixed
     * @Date : 2022/4/6 下午11:02
     * @Author : By Jensen
     */
    public static function TransferFrom($fromAddress, $toAddress, $OnlyToken)
    {
        $url = self::$url . '/bsc/nft/transferFrom';
        $param = ['fromAddress' => $fromAddress, 'toAddress' => $toAddress, 'tokenId' => $OnlyToken];
        $data = self::toCurl($url, $param);
        $data = json_decode($data, true);
        return $data;
    }


    /*
     * array(3) {
  ["code"]=>
  int(200)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(1) {
    ["transactionReceipt"]=>
    array(19) {
      ["transactionHash"]=>
      string(66) "0xbb995cd155e2064409fb06598ccdea6c26712820febf8774a9e78f2df305fd46"
      ["transactionIndex"]=>
      int(76)
      ["blockHash"]=>
      string(66) "0x9e81ec8fe65fb776fba10b28918ac6d773a4b3e38adab28e656806f4819418a9"
      ["blockNumber"]=>
      int(16679989)
      ["cumulativeGasUsed"]=>
      int(7099762)
      ["gasUsed"]=>
      int(20885)
      ["contractAddress"]=>
      NULL
      ["root"]=>
      NULL
      ["status"]=>
      string(3) "0x1"
      ["from"]=>
      string(42) "0x664ee44c8145ab2bdef94fcf458ef37cbead8879"
      ["to"]=>
      string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
      ["logs"]=>
      array(2) {
        [0]=>
        array(13) {
          ["removed"]=>
          bool(false)
          ["logIndex"]=>
          int(191)
          ["transactionIndex"]=>
          int(76)
          ["transactionHash"]=>
          string(66) "0xbb995cd155e2064409fb06598ccdea6c26712820febf8774a9e78f2df305fd46"
          ["blockHash"]=>
          string(66) "0x9e81ec8fe65fb776fba10b28918ac6d773a4b3e38adab28e656806f4819418a9"
          ["blockNumber"]=>
          int(16679989)
          ["address"]=>
          string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
          ["data"]=>
          string(2) "0x"
          ["type"]=>
          NULL
          ["topics"]=>
          array(4) {
            [0]=>
            string(66) "0x8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925"
            [1]=>
            string(66) "0x000000000000000000000000926b997959aed065cdf643b54ffca16298f8fff4"
            [2]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000000"
            [3]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000001"
          }
          ["transactionIndexRaw"]=>
          string(4) "0x4c"
          ["blockNumberRaw"]=>
          string(8) "0xfe8435"
          ["logIndexRaw"]=>
          string(4) "0xbf"
        }
        [1]=>
        array(13) {
          ["removed"]=>
          bool(false)
          ["logIndex"]=>
          int(192)
          ["transactionIndex"]=>
          int(76)
          ["transactionHash"]=>
          string(66) "0xbb995cd155e2064409fb06598ccdea6c26712820febf8774a9e78f2df305fd46"
          ["blockHash"]=>
          string(66) "0x9e81ec8fe65fb776fba10b28918ac6d773a4b3e38adab28e656806f4819418a9"
          ["blockNumber"]=>
          int(16679989)
          ["address"]=>
          string(42) "0xae6d7607ac1a75c466858f4150d84fa4db46deb9"
          ["data"]=>
          string(2) "0x"
          ["type"]=>
          NULL
          ["topics"]=>
          array(4) {
            [0]=>
            string(66) "0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef"
            [1]=>
            string(66) "0x000000000000000000000000926b997959aed065cdf643b54ffca16298f8fff4"
            [2]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000000"
            [3]=>
            string(66) "0x0000000000000000000000000000000000000000000000000000000000000001"
          }
          ["transactionIndexRaw"]=>
          string(4) "0x4c"
          ["blockNumberRaw"]=>
          string(8) "0xfe8435"
          ["logIndexRaw"]=>
          string(4) "0xc0"
        }
      }
      ["logsBloom"]=>
      string(514) "0x00000000000000000000000000000000000000000000000000000000000000000000000200000000000000000020000000000000000000000000000000240000000000000100000000000008000000000000010000040000000000000000000000000000020000000000000000000800000000000010000000000010000000000000000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000060000010000000000000000000000000000000000000004000000000000000000000"
      ["revertReason"]=>
      NULL
      ["transactionIndexRaw"]=>
      string(4) "0x4c"
      ["blockNumberRaw"]=>
      string(8) "0xfe8435"
      ["cumulativeGasUsedRaw"]=>
      string(8) "0x6c5572"
      ["gasUsedRaw"]=>
      string(6) "0x5195"
      ["statusOK"]=>
      bool(true)
    }
  }
}
     * */

    /**
     * @Explain : 销毁
     * @param $OnlyToken
     * @return mixed
     * @Date : 2022/4/6 下午11:03
     * @Author : By Jensen
     */
    public static function Burn($OnlyToken)
    {
        $url = self::$url . '/bsc/nft/burn';
        $param = ['tokenId' => $OnlyToken];
        $data = self::toCurl($url, $param);
        $data = json_decode($data, true);
        return $data;
    }


    /**
     * @Explain : 插入藏品信息
     * @param $id
     * @param $owner
     * @param $name
     * @param $info
     * @param $time
     * @return mixed
     * @throws ClientException
     * @Date : 2022/3/31 下午2:19
     * @Author : By Jensen
     */
    public static function insertAnts($id, $owner, $name, $info, $time)
    {
        // 初始化客户端
        $client = new AntCloudClient(
            "https://prodapigw.cloud.alipay.com/gateway.do",
            "LTAI5tRYhrWRdxr6NozbtzhT",
            "XZ0yhx8EJxcp3OLoBxgHRc5JllVnCo"
        );
        // 构建请求
        $request = array(
            "app_did" => "did:mychain:f2ebfac9f82be70cb918bdc17f0945660b68ffc58854abac635a33844c6fdf1c",
            "schema_name" => self::$table,
            "attributes" => [
                [
                    "name" => 'id',
                    "value" => $id,
                ],
                [
                    "name" => 'owner',
                    "value" => $owner,
                ],
                [
                    "name" => 'name',
                    "value" => $name,
                ],
                [
                    "name" => "info",
                    "value" => $info
                ],
                [
                    "name" => "time",
                    "value" => $time
                ],
            ],
            "method" => "blockchain.appex.solution.fastnotary.save",
            "version" => "1.0",
            "product_instance_id" => "appex",
        );
        // 发送调用请求，解析响应结果
        $response = $client->execute($request);
        return $response;
    }

    /**
     * @Explain : 查询藏品信息
     * @param $id
     * @return mixed
     * @throws ClientException
     * @Date : 2022/3/31 下午2:20
     * @Author : By Jensen
     */
    public static function selectAnts($id)
    {
        // 初始化客户端
        $client = new AntCloudClient(
            "https://prodapigw.cloud.alipay.com/gateway.do",
            "LTAI5tRYhrWRdxr6NozbtzhT",
            "XZ0yhx8EJxcp3OLoBxgHRc5JllVnCo"
        );
        // 构建请求
        $request = array(
            "app_did" => "did:mychain:f2ebfac9f82be70cb918bdc17f0945660b68ffc58854abac635a33844c6fdf1c",
            "biz_index_key_value" => $id,
            "schema_name" => self::$table,
            "method" => "blockchain.appex.solution.fastnotary.query",
            "version" => "1.0",
            "product_instance_id" => "appex",
        );

        // 发送调用请求，解析响应结果
        $response = $client->execute($request);
        return $response;
    }

    /**
     * @Explain : Curl请求
     * @param $url
     * @param array $param
     * @return bool|string|string[]
     * @Date : 2022/4/5 下午7:21
     * @Author : By Jensen
     */
    public static function toCurl($url, $param = array())
    {
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Token: ' . self::$Token]);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        $data = str_replace("\"", '"', $data);
        return $data;
    }
}