<?php

namespace Fql\FqlAnnotation\annotation\parser\impl;

use Fql\FqlAnnotation\bean\BeanDefinition;
use Fql\FqlAnnotation\context\ApplicationContext;
use think\Exception;

class ComponentParserFactory implements AnnotationParserFactory
{
    public static  $beanDefinition;
    public static  $beanPostProcessor;

    public  static  function parse($className)
    {
        self::$beanDefinition = null;
        self::$beanPostProcessor = null;
        if(!(class_exists($className) || interface_exists($className))){
            return null;
        }
        $clazz = new \ReflectionClass($className);
        $docReader = new AnnotationParserImpl();
        $annotation = $docReader->doReader($clazz->getDocComment());
        if($annotation===false){
         return null;
        }
        if(array_key_exists('component',$annotation)){
            $beanDefinition = new BeanDefinition();
            $beanDefinition->setClazz($clazz);
            $beanDefinition->setScope('singleton');
            $simpleClazz =  lcfirst(substr($className,strrpos($className,'\\',0)+1));
            self::$beanDefinition['key'] = $simpleClazz;
            self::$beanDefinition['value'] = $beanDefinition;
            self::beanProssor($clazz);
        }

    }

    public  static  function beanProssor(\ReflectionClass $clazz){
//        $clazz = str_replace("\\",'\\',$clazz);
//        if(class_exists($clazz)){
            $interfaces =  $clazz->getInterfaceNames();
            foreach ($interfaces as $interface){
                if("Fql\\FqlAnnotation\\bean\\BeanPostProcessor" == $interface){
                    $bean = $clazz->newInstance();
                    self::$beanPostProcessor = $bean;
                }
            }
//        }
    }
}