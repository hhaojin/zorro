<?php

namespace Zorro\Attribute\Collector;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;
use Zorro\Aspect\Aspect;
use Zorro\Attribute\AttributeHandler;
use Zorro\Attribute\CustomAttribute;
use Zorro\Attribute\CustomAttributeInterface;
use Zorro\BeanFactory;

class ClassAttrubuteCollector
{
    /**
     * @param ReflectionClass $rf
     * @param object $instance
     * @return void
     */
    static function collect(ReflectionClass $rf, object $bean,): void
    {

    }
}
