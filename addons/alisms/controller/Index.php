<?php

namespace addons\alisms\controller;

use think\addons\Controller;

/**
 * 阿里短信
 */
class Index extends Controller
{

    protected $model = null;

    public function _initialize()
    {
        if (!\app\admin\library\Auth::instance()->id) {
            $this->error('暂无权限浏览');
        }
        parent::_initialize();
    }

    public function index()
    {
        return $this->view->fetch();
    }

    public function send()
    {
        $config = get_addon_config('alisms');
        $mobile = $this->request->post('mobile');
        $template = $this->request->post('template');
        $sign = $this->request->post('sign');
        if (!$mobile || !$template) {
            $this->error('手机号、模板ID不能为空');
        }
        $sign = $sign ? $sign : $config['sign'];
        $param = (array)json_decode($this->request->post('param', '', 'trim'));
        $alisms = new \addons\alisms\library\Alisms();
        $ret = $alisms->mobile($mobile)
            ->template($template)
            ->sign($sign)
            ->param($param)
            ->send();
        if ($ret) {
            $this->success("发送成功");
        } else {
            $this->error("发送失败！失败原因：" . $alisms->getError());
        }
    }

}
