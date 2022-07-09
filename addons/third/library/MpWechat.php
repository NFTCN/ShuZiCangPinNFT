<?php

namespace addons\third\library;

use EasyWeChat\Factory;
use fast\Http;
use think\Config;
use think\Session;

/**
 * 微信小程序
 */
class MpWechat
{
    /**
     * 配置信息
     * @var array
     */
    private $config = [];
    /**
     * @var \EasyWeChat\MiniProgram\Application
     */
    private $app;

    public function __construct($options = [])
    {
        if ($config = Config::get('third.mp-wechat')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
        $this->app = Factory::miniProgram([
            'app_id' => $this->config['app_id'],
            'secret' => $this->config['app_secret'],
            'response_type' => 'array',
        ]);

    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo($params = [])
    {
        $params = $params ? $params : request()->get();

        if (isset($params['code'])) {
            $data = $this->app->auth->session($params['code']);
            if ($access_token) {
                $openid = isset($data['openid']) ? $data['openid'] : '';
                $unionid = isset($data['unionid']) ? $data['unionid'] : '';
                $data = [
                    'access_token'  => $data['session_key'],
                    'refresh_token' => $data['session_key'],
                    'expires_in'    => 7100,
                    'openid'        => $openid,
                    'unionid'       => $unionid,
                    'userinfo'      => []
                ];
                return $data;
            }
        }
        return [];
    }
}
