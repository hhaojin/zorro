<?php

namespace Zorro;

use DI\Container;
use DI\ContainerBuilder;

class BeanFactory
{
    /** @var Container */
    private static $container;

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
            return false;
        }
    }

    public static function setBean($beanName, $bean)
    {
        self::$container->set($beanName, $bean);
    }

}
