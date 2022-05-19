<?php
namespace Fql\FqlAnnotation\bean;

use Fql\FqlAnnotation\config\Config;
use Fql\FqlAnnotation\utils\DynamicProxy;

/**
 * @component
 */

class CommonBeanProcessor implements BeanPostProcessor
{
    function postProcessBeforeInitialization($bean,$beanName,$beanDefinition)
    {
        //注入前置和后置操作
        $proxyBean = new DynamicProxy($bean);
        $config =  Config::getConfig('HookConfig.php');
        foreach ($config as $hookBeanName => $classPath){
            if($beanName == $hookBeanName){
                $clazz = new \ReflectionClass($classPath);
                $instance = $clazz->newInstance();
                $proxyBean->setTask($instance);
            }
        }
        return $proxyBean;
    }

    function postProcessAfterInitialization($bean,$beanName,$beanDefinition)
    {

        return $bean;
    }

}