<?php

namespace Fql\FqlAnnotation\config;

use function Composer\Autoload\includeFile;

class Config
{
    private static $config = [];

    static function requireFile($file){
        //获取文件路径
        $filePath = dirname(__FILE__).DIRECTORY_SEPARATOR.$file;
        $configArray  =  include $filePath;
        self::$config = array_merge(self::$config,$configArray);
    }

    static function  getConfig($configFile){
        self::requireFile($configFile);
        return self::$config;
    }
}