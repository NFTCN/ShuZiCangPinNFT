<?php

namespace AntCloudSDKCore\Autoloader;

spl_autoload_register("AntCloudSDKCore\Autoloader\Autoloader::autoload");

class Autoloader
{
    private static $autoloadPathArray = array(
        "AntCloudSDKCore",
        "AntCloudSDKCore/Auth",
        "AntCloudSDKCore/Http",
        "AntCloudSDKCore/Profile",
        "AntCloudSDKCore/Regions",
        "AntCloudSDKCore/Exception"
    );

    public static function autoload($className)
    {
        $file = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $className . ".php";
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        if (is_file($file)) {
            include_once $file;
        }
    }

    public static function addAutoloadPath($path)
    {
        array_push(self::$autoloadPathArray, $path);
    }
}