<?php

namespace addons\alisms;

use think\Addons;

/**
 * Alisms
 */
class Alisms extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 短信发送行为
     * @param array $params 必须包含mobile,event,code
     * @return  boolean
     */
    public function smsSend(&$params)
    {
        $config = get_addon_config('alisms');
        $alisms = new \addons\alisms\library\Alisms();
        $result = $alisms->mobile($params['mobile'])
            ->template($config['template'][$params['event']])
            ->param(['code' => $params['code']])
            ->send();
        return $result;
    }

    /**
     * 短信发送通知
     * @param array $params 必须包含 mobile,event,msg
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $config = get_addon_config('alisms');
        $alisms = \addons\alisms\library\Alisms::instance();
        if (isset($params['msg'])) {
            if (is_array($params['msg'])) {
                $param = $params['msg'];
            } else {
                parse_str($params['msg'], $param);
            }
        } else {
            $param = [];
        }
        $param = $param ? $param : [];
        $params['template'] = isset($params['template']) ? $params['template'] : (isset($params['event']) && isset($config['template'][$params['event']]) ? $config['template'][$params['event']] : '');
        $result = $alisms->mobile($params['mobile'])
            ->template($params['template'])
            ->param($param)
            ->send();
        return $result;
    }

    /**
     * 检测验证是否正确
     * @param   $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return true;
    }
}
