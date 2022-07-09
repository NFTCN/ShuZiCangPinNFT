<?php

namespace addons\elfinder;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Elfinder extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'elfinder',
                'title'   => '文件管理器',
                'icon'    => 'fa fa-terminal',
                'sublist' => [
                    [
                        'name' => 'elfinder/index', 'title' => '查看',
                    ],
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
        Menu::delete('elfinder');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('elfinder');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('elfinder');
        return true;
    }

}
