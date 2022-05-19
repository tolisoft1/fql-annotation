<?php

namespace Fql\FqlAnnotation\bean;

interface BeanPostProcessor
{
    function postProcessBeforeInitialization($bean,$beanName,$beanDefinition);
    function postProcessAfterInitialization($bean,$beanName,$beanDefinition);
}