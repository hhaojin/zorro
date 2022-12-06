<?php

namespace Zorro\Attribute;

use ReflectionClass;
use Zorro\Attribute\Collector\ClassCollector;
use Zorro\Attribute\Collector\MethodCollector;
use Zorro\Attribute\Collector\PropertiesCollector;
use Zorro\BeanFactory;

class AttributeCollector
{
    private static $collected = [];

    public static function collectAttributes(array $classes, array $namespaces)
    {
        foreach ($classes as $class) {
            if (!self::shouldCollect($class, $namespaces)) {
                continue;
            }
            $bean = BeanFactory::make($class);
            $rf = new ReflectionClass($bean);
            self::collectAttribute($rf, $bean);
        }
        self::$collected = null;
    }

    public static function collectAttribute(ReflectionClass $rf, $instance)
    {
        $instanceName = get_class($instance);
        if (isset(self::$collected[$instanceName])) {
            return;
        }
        $properties = $rf->getProperties();
        foreach ($properties as $property) {  //先递归处理属性类
            $propertyVal = $property->getValue($instance);
            if (is_object($propertyVal)) {
                self::collectAttribute(new ReflectionClass($propertyVal), $propertyVal);
                if (!is_null($propertyVal)) {
                    PropertiesCollector::collect($property, $propertyVal);
                    $property->setAccessible(true);
                    $propertyBean = BeanFactory::getBean(get_class($propertyVal)); //此时属性可能已经被替换成了切面代理类
                    $property->setValue($instance, $propertyBean);
                }
            } else {
                PropertiesCollector::collect($property, $instance);
            }
        }
        BeanFactory::setBean($instanceName, $instance);
        $methods = $rf->getMethods();
        foreach ($methods as $method) {
            $bean = BeanFactory::getBean($instanceName);
            MethodCollector::collect($method, $bean);
        }
        ClassCollector::collect($rf, $instance);
        self::$collected[$instanceName] = true;
    }

    protected static function shouldCollect($class, $namespaces): bool
    {
        foreach ($namespaces as $namespace) {
            if (strstr($class, $namespace)) {
                return true;
            }
        }
        return false;
    }
}
