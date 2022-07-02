<?php

namespace Zorro;

use DI\Container;
use DI\ContainerBuilder;

class BeanFactory
{
    /** @var Container */
    public static $container;

    public static function _init()
    {
        $builder = new ContainerBuilder();
        self::$container = $builder->build();
    }

    public static function getBean($beanName)
    {
        try {
            return self::$container->get($beanName);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    public static function hasBean($beanName)
    {
        return self::$container->has($beanName);
    }

    public static function make($beanName)
    {
        return self::$container->make($beanName);
    }

    public static function setBean($beanName, $bean)
    {
        self::$container->set($beanName, $bean);
    }

}
