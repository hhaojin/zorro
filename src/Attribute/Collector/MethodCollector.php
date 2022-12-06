<?php

namespace Zorro\Attribute\Collector;

use Attribute;
use ReflectionAttribute;
use ReflectionMethod;
use Zorro\Aspect\Aspect;
use Zorro\Attribute\CustomAttributeInterface;
use Zorro\BeanFactory;

class MethodCollector extends CollectorAbstract
{

    /**
     * @param ReflectionMethod $rf
     * @param object $instance
     * @return void
     */
    public static function collect($rf, $bean): void
    {
        foreach ($rf->getAttributes() as $attr) {
            if (!self::shouldHandle($attr)) {
                continue;
            }
            //是否自定义注解？
            AttributeHandler::collectCustomAttribute($attr);
            self::handle($rf, $bean, $attr);
        }
    }

    protected static function handle(ReflectionMethod $rf, object $instance, ReflectionAttribute $attr): void
    {
        $handlerName = AttributeHandler::$handler[$attr->getName()];
        $handler = BeanFactory::getBean($handlerName);
        if (!$handler instanceof CustomAttributeInterface) {
            return;
        }
        if ($attr->getTarget() & Attribute::TARGET_METHOD !== Attribute::TARGET_METHOD) {
            return;
        }
        $fn = $handler->handle($rf, $instance, $attr);
        $className = get_class($instance);
        if ($className == Aspect::class) { //如果已经是切片类了， 那么则合并切点
            self::mergePoint($rf, $instance, $fn);
        } else {
            $aspect = new Aspect($rf->getName(), $instance, $fn, $rf->getClosure($instance));
            $instance = $aspect;
        }
        //echo sprintf("setbean ---->  %s  --->  %s\n", $className, get_class($instance));
        BeanFactory::setBean($className, $instance);
    }

    protected static function mergePoint(ReflectionMethod $rf, object $proxy, \Closure $fn): void
    {
        $aspectRf = new \ReflectionClass($proxy);
        $points = $aspectRf->getProperty("points");
        $points->setAccessible(true);
        //已有的切点
        $value = $points->getValue($proxy);

        $value = array_merge($value, [$rf->getName() => $fn]);
        $points->setValue($proxy, $value);
    }
}