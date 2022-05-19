<?php
namespace Fql\FqlAnnotation\scanner;
class Scanner
{
    private static $files;
    static  function  scannerDir($dir){
        if($handle = opendir($dir)){
            while(($file=readdir($handle))!==false){
                if($file!="." && $file!=".."){
                    if(is_dir($dir.DIRECTORY_SEPARATOR.$file)){
                         self::scannerDir($dir.DIRECTORY_SEPARATOR.$file);
                    }else{
                        if(self::getFileExtension($file) == 'php'){
                            self::$files[$file] = $dir.DIRECTORY_SEPARATOR.$file;
                        }
                    }
                }
            }
            closedir($handle);
            return self::$files;
        }
    }


    static  function getFileExtension($file){
        return pathinfo($file,PATHINFO_EXTENSION);
    }
}