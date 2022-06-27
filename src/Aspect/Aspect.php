<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/21
 * Time: 22:33
 */


namespace Zorro\Aspect;


class Aspect
{
    /** @var object */
    protected $originClass;

    /** @var \Closure[] */
    protected $points = [];

    public function __construct($name, \Closure ...$pointHandle)
    {
        $this->points[$name] = $pointHandle;
    }

    public function __call(string $name, array $arguments)
    {
        if (!array_key_exists($name, $this->points)) {
            return call_user_func_array([$this->originClass, $name], $arguments);
        }
        $ctx = new PointContext($this->points[$name]);
        return $ctx->next($arguments);
    }
}
