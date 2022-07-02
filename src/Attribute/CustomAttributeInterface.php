<?php

namespace Zorro\Attribute;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface CustomAttributeInterface
{
    /**
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflect 被打注解的目标反射对象
     * @param object $instance 类实例
     * @param ReflectionAttribute $attribute 注解对象
     * @return \Closure function(\Zorro\Aspect\JoinPoint $joinPoint, array $args)
     */
    public function handle($reflect, $instance, $attribute): \Closure;
}
