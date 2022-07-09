<?php

namespace addons\nft;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Nft extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = include ADDON_PATH . 'nft' . DS . 'data' . DS . 'menu.php';
        Menu::create($menu);
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
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        
        return true;
    }

}
