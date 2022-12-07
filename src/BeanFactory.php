<?php

namespace Zorro;

use DI\Container;
use DI\ContainerBuilder;
use Zorro\Attribute\AttributeCollector;

class BeanFactory
{
    /** @var Container */
    protected static $container;

    protected static $resolved = [];

    public static function create()
    {
        if (self::$container === null) {
            $builder = new ContainerBuilder();
            self::$container = $builder->build();
        }
        return self::$container;
    }

    public static function getBean($beanName)
    {
        if (!array_key_exists($beanName, self::$resolved)) {
            $bean = BeanFactory::make($beanName);
            $rf = new \ReflectionClass($bean);
            AttributeCollector::collectAttribute($rf, $bean);
        }
        return self::create()->get($beanName);
    }

    public static function hasBean($beanName)
    {
        return self::create()->has($beanName);
    }

    public static function make($beanName)
    {
        return self::create()->make($beanName);
    }

    public static function setBean($beanName, $bean)
    {
        self::create()->set($beanName, $bean);
        self::$resolved[$beanName] = null;
    }

}
