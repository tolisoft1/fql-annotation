<?php

namespace Fql\FqlAnnotation\annotation\parser\impl;

use Fql\FqlAnnotation\bean\BeanDefinition;
use Fql\FqlAnnotation\context\ApplicationContext;
use think\Exception;

class AutowiredParserFactory implements AnnotationParserFactory
{

    public  static  function parse($className)
    {
        $docReader = new AnnotationParserImpl();
        $annotation = $docReader->doReader($property->getDocComment());
        if(array_key_exists('Autowired',$annotation)){

        }
    }
}