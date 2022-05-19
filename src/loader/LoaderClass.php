<?php
namespace Fql\FqlAnnotation\loader;

class LoaderClass
{

    static  function load($files){
        foreach ($files as $file){
            require_once $file;
        }
    }
}