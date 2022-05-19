<?php

namespace Fql\FqlAnnotation\context;

use Fql\FqlAnnotation\annotation\parser\impl\AnnotationParserImpl;
use Fql\FqlAnnotation\annotation\parser\impl\ComponentParserFactory;
use Fql\FqlAnnotation\bean\BeanDefinition;
use Fql\FqlAnnotation\bean\BeanPostProcessor;
use Fql\FqlAnnotation\loader\LoaderClass;
use Fql\FqlAnnotation\scanner\Scanner;

class ApplicationContext
{
    private $config;
    private $baenFactory;
    private $docParser;
    private $beanDefinition = []; //扫描加载了commponent的类
    private $singletonObjects; //单例池,成品->一级缓存
    private $earlySingletonObjects; //半成品【原始对象，或者是半成品代理对象】，创建中的对象->二级缓存
    private $beanPostProcessorList = []; //处理器
    private $singletonFactories; //三级缓存
    private $singletonsCurrentlyInCreation; //标明正在创建的对象


    /**
     * @return mixed
     */
    public function getBeanDefinition($beanName)
    {
        return $this->beanDefinition[$beanName];
    }

    /**
     * @param mixed $beanDefinition
     */
    public function setBeanDefinition($key,$beanDefinition): void
    {
        $this->beanDefinition[$key] = $beanDefinition;
    }

    public function __construct($config)
    {
        $this->config = $config;
        $this->docParser = new AnnotationParserImpl();
        $files = Scanner::scannerDir($this->config['scannerPackage']); //扫描文件
        $selfFiles = Scanner::scannerDir(dirname(__FILE__)."/../../src"); //扫描文件
        $files = array_unique(array_merge($files,$selfFiles));
        LoaderClass::load($files); //加载文件
        foreach ($files as $key=>$file){
            $className =$this->parserClassNamespaceName($file);
            $arr = ComponentParserFactory::parse($className);
            if(!empty(ComponentParserFactory::$beanDefinition)){
                $this->beanDefinition[ComponentParserFactory::$beanDefinition['key']] = ComponentParserFactory::$beanDefinition['value'];
            }
            if(!empty(ComponentParserFactory::$beanPostProcessor)){
                array_push($this->beanPostProcessorList,ComponentParserFactory::$beanPostProcessor);
            }
        }
        //实例化单例
        foreach ($this->beanDefinition as $k=>$bean){
            if($bean->getScope()=='singleton'){
                if(!isset($this->singletonObjects[$k])){
                    $bean = $this->createBean($k,$bean);
                    $this->singletonObjects[$k] = $bean;
                }
            }
        }
    }

    public function parserClassNamespaceName($classPath){
        $localNameSpacePath = dirname(__FILE__)."/../../src";
        if(strrpos($classPath,$localNameSpacePath)!== false){
            $classNameSpaceName = str_replace($localNameSpacePath,"Fql/FqlAnnotation",$classPath);
            $className =  substr($classNameSpaceName,0,strrpos($classNameSpaceName,'.'));
            return  str_replace("/",'\\',$className);
        }



        //获取扫描namespace
        $namespace = $this->config['namespace'];
        $namespaceDir = $this->config['namespaceDir'];
        $classNameSpaceName = str_replace($namespaceDir,$namespace,$classPath);
        $className =  substr($classNameSpaceName,0,strrpos($classNameSpaceName,'.'));
        return  str_replace("/",'\\',$className);
    }


    public function getBean($beanName){
        if(array_key_exists($beanName,$this->beanDefinition)){
            $beanDefinition = $this->getBeanDefinition($beanName);
            if($beanDefinition->getScope()=='singleton'){
                if(!isset($this->singletonObjects[$beanName])){
                    if(!isset($this->earlySingletonObjects[$beanName])){
                        $bean = $this->createBean($beanName,$beanDefinition);
                    }else{
                        //提前aop
                        $bean = $this->earlySingletonObjects[$beanName];
                    }
                    $this->singletonObjects[$beanName] = $bean;
                }
                return $this->singletonObjects[$beanName];
            }else{
                return $this->createBean($beanName,$beanDefinition);
            }
        }
        return null;
    }

    public function createBean($beanName, BeanDefinition $beanDefinition){
        try {
            $clazz = $beanDefinition->getClazz();
            $instance = $clazz->newInstance();        //实例化
            //放入正在创建中的bean里面
            $this->singletonsCurrentlyInCreation[$beanName] = $instance;
            //放入不成熟的bean
            $this->earlySingletonObjects[$beanName] = $instance;
            $properties =  $clazz->getProperties();   //依赖注入，属性赋值
            foreach ($properties as $property){
                if($property->getDocComment() ==false){
                    continue;
                }
                $annotation = $this->docParser->doReader($property->getDocComment());
                if(!empty($annotation)){
                    if(array_key_exists('Autowired',$annotation)){
                        $propertyName = $property->getName();
                        $bean =  $this->getBean($propertyName);
                        $property->setAccessible(true);
                        $property->setValue($instance,$bean);
                    }
                }

            }

//            if(in_array('BeanPostProcessor',$clazz->getInterfaceNames())){
//                array_push($this->beanPostProcessorList,$instance);
//            }

            //初始化前
            foreach ($this->beanPostProcessorList as $beanPostProcesssor){
                $instance = $beanPostProcesssor->postProcessBeforeInitialization($instance,$beanName,$beanDefinition);
            }
            //初始化

            //初始化后
            foreach ($this->beanPostProcessorList as $beanPostProcesssor){
                $instance = $beanPostProcesssor->postProcessAfterInitialization($instance,$beanName,$beanDefinition);
            }

            unset($this->singletonsCurrentlyInCreation[$beanName]);
            unset($this->earlySingletonObjects[$beanName]);
            return $instance;
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }
}