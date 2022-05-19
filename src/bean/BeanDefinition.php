<?php
namespace Fql\FqlAnnotation\bean;

class BeanDefinition
{

    private $clazz;
    private $scope;

    /**
     * @return mixed
     */
    public function getClazz()
    {
        return $this->clazz;
    }

    /**
     * @param mixed $clazz
     */
    public function setClazz($clazz): void
    {
        $this->clazz = $clazz;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     */
    public function setScope($scope): void
    {
        $this->scope = $scope;
    }


}