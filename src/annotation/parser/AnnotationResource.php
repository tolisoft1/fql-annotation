<?php
namespace Fql\FqlAnnotation\annotation\parser;

class AnnotationResource
{
    /**
     * 解析bean注解
     *
     * @param string $className
     *
     * @return null
     */
    public function parseBeanAnnotations(string $className)
    {
        if (!class_exists($className) && !interface_exists($className)) {
            return null;
        }

        // 注解解析器
        $reader           = new AnnotationReader();
        $reader           = $this->addIgnoredNames($reader);
        $reflectionClass  = new \ReflectionClass($className);
        $classAnnotations = $reader->getClassAnnotations($reflectionClass);

        // 没有类注解不解析其它注解
        if (empty($classAnnotations)) {
            return;
        }

        foreach ($classAnnotations as $classAnnotation) {
            $this->annotations[$className]['class'][get_class($classAnnotation)] = $classAnnotation;
        }

        // 解析属性
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $propertyName        = $property->getName();
            $propertyAnnotations = $reader->getPropertyAnnotations($property);
            foreach ($propertyAnnotations as $propertyAnnotation) {
                $this->annotations[$className]['property'][$propertyName][get_class($propertyAnnotation)] = $propertyAnnotation;
            }
        }

        // 解析方法
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $methodName = $method->getName();

            // 解析方法注解
            $methodAnnotations = $reader->getMethodAnnotations($method);

            foreach ($methodAnnotations as $methodAnnotation) {
                $this->annotations[$className]['method'][$methodName][get_class($methodAnnotation)][] = $methodAnnotation;
            }
        }
    }
}