<?php

namespace app\common\library;

use addons\nft\library\Common;
use addons\nft\model\Identify;
use fast\Http;
use think\Config;

class Safrvcert
{

    const ID2META = "https://id2meta.market.alicloudapi.com/id2meta";

    /**
     * 单例对象
     */
    protected static $instance;

    /**
     * phpmailer对象
     */
    protected $method = "GET";

    /**
     * 错误内容
     */
    protected $error = '';


    /**
     * 身份证
     * @var string
     */
    protected $identify_num = '';

    /**
     * 姓名
     * @var string
     */
    protected $user_name = '';

    protected $headers = [];

    /**
     * @param string $identify_num
     */
    public function setIdentifyNum(string $identify_num): Safrvcert
    {
        $this->identify_num = $identify_num;
        return $this;
    }

    /**
     * @param string $user_name
     */
    public function setUserName(string $user_name): Safrvcert
    {
        $this->user_name = $user_name;
        return $this;
    }

    /**
     * 默认配置
     */
    public $options = [
    ];

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Safrvcert
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options = [])
    {
        $config = get_addon_config('nft');
        $options['appcode'] =$config['ini']['identify_appcode'];

        if ($config = Config::get('site.safrvcert')) {
            $this->options = array_merge($this->options, $config);
        }
        $this->options = array_merge($this->options, $options);
        $this->headers[CURLOPT_HTTPHEADER] = [
            "Authorization:APPCODE " . $this->options['appcode']
        ];
    }


    /**
     * 获取最后产生的错误
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误
     * @param string $error 信息信息
     */
    protected function setError($error)
    {
        $this->error = $error;
    }


    /**
     * 验证
     * @return boolean
     */
    public function auth()
    {
        $result = false;
        $identifyNum = Common::hideStr($this->identify_num,6,6,4,'*');
        //检查是否有实名过
        if(Identify::where('identify',$identifyNum)->count() > 0){
            $this->setError('此身份已被注册并实名过');
            return false;
        }
        try {
            $queryarr = [
                'identifyNum'=>$this->identify_num,
                'userName'=>$this->user_name,
            ];
            $response = Http::get(self::ID2META, $queryarr,$this->headers);
            $ret = (array)json_decode($response, true);
            if(!empty($ret) && $ret['code'] == 200){
                \think\Log::info('身份认证结果'.json_encode($ret));
                if($ret['data']['bizCode'] == 2){
                    $this->setError('校验不一致');
                    $result = false;
                }elseif($ret['data']['bizCode'] == 3){
                    $this->setError('查无记录');
                    $result = false;
                }else{
                    $result = true;
                }
            }else{
                $this->setError('身份认证失败');
            }
        }catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $result;
    }

}
