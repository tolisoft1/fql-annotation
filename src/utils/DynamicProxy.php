<?php
namespace Fql\FqlAnnotation\utils;

class DynamicProxy
{
    private $target;
    private $task = [];

    /**
     * @return array
     */
    public function getTask(): array
    {
        return $this->task;
    }

    /**
     * @param array $task
     */
    public function setTask($obj): void
    {
        $this->task[] = $obj;
    }

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function __call($name, $arguments)
    {
        // 使用反射类
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod($name);
        // 前置操作
        foreach ($this->task  as $index => $obj){
            try {
                $arguments =  $obj->beforeHandle($arguments);
            }catch (\Exception $e){
                echo 'error:',$e->getMessage(),PHP_EOL;
                return;
            }
        }
        // 执行反射函数
        $return = $method->invokeArgs($this->target, $arguments);
        foreach ($this->task  as $index => $obj){
            try {
                $return =  $obj->afterHandle($return);
            }catch (\Exception $e){
                echo 'error:',$e->getMessage(),PHP_EOL;
                return;
            }
        }
        return $return;
        // 后置操作
    }
}
