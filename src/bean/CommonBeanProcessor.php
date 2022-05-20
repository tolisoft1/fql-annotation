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
        $proxyBean = $this->injectInspect($beanName,$config,$proxyBean);
        return $proxyBean;
    }

    function postProcessAfterInitialization($bean,$beanName,$beanDefinition)
    {

        return $bean;
    }

    /**
     * 注入前置或后置操作
     * @param $config
     * @param $proxyBean
     * @return mixed
     * @throws \ReflectionException
     */
    private function injectInspect($beanName,$config,$proxyBean){
        foreach ($config as $hookBeanName => $classPath){
            if($beanName == $hookBeanName){
                $clazz = new \ReflectionClass($classPath);
                $instance = $clazz->newInstance();
                $proxyBean->setTask($instance);
            }
        }
        return $proxyBean;
    }

}