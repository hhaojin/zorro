<?php

namespace Zorro\Attribute\Collector;

use Attribute;
use ReflectionAttribute;
use ReflectionProperty;
use Zorro\Attribute\CustomAttributeInterface;
use Zorro\BeanFactory;

class PropertiesCollector extends CollectorAbstract
{
    /**
     * @param ReflectionProperty $rf
     * @param object $bean
     * @return void
     */
    static function collect($rf, $bean): void
    {
        foreach ($rf->getAttributes() as $attr) {
            if (!self::shouldHandle($attr)) {
                continue;
            }
            AttributeHandler::collectCustomAttribute($attr);
            self::handle($rf, $bean, $attr);
        }
    }

    protected static function handle(ReflectionProperty $rf, object $instance, ReflectionAttribute $attr): void
    {
        if ($attr->getTarget() & Attribute::TARGET_PROPERTY !== Attribute::TARGET_PROPERTY) {
            return;
        }
        $handlerName = AttributeHandler::$handler[$attr->getName()];
        $handler = BeanFactory::getBean($handlerName);
        if (!$handler instanceof CustomAttributeInterface) {
            return;
        }
        $handler->handle($rf, $instance, $attr);
    }
}