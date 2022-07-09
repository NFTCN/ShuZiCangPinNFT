<?php

namespace addons\third;

use app\common\library\Menu;
use think\Addons;

/**
 * 第三方登录
 */
class Third extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'third',
                'title'   => '第三方登录管理',
                'icon'    => 'fa fa-users',
                'sublist' => [
                    [
                        "name"  => "third/index",
                        "title" => "查看"
                    ],
                    [
                        "name"  => "third/del",
                        "title" => "删除"
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("third");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("third");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("third");
        return true;
    }

    /**
     * @param $params
     */
    public function configInit(&$params)
    {
        $config = $this->getConfig();
        $params['third'] = ['status' => explode(',', $config['status'])];
    }
}
