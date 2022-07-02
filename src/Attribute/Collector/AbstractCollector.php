<?php

namespace Zorro\Attribute\Collector;

use Attribute;
use ReflectionAttribute;
use Zorro\Attribute\AttributeHandler;
use Zorro\Attribute\CustomAttribute;
use Zorro\Attribute\CustomAttributeInterface;
use Zorro\BeanFactory;

abstract class AbstractCollector
{
    protected function handleAttrs(array $attrs)
    {
        foreach ($attrs as $attr) {
            if (!$this->shouldHandle($attr)) {
                continue;
            }
            //是否自定义注解？
            AttributeHandler::collectCustomAttribute($attr);
            $this->handle($attr);
        }
    }

    protected function shouldHandle(ReflectionAttribute $attr): bool
    {
        $name = $attr->getName();
        if ($name === CustomAttribute::class) {
            return false;
        }
        if ($name === Attribute::class) {
            return false;
        }
        return true;
    }

    protected function handle(ReflectionAttribute $attr)
    {

        $handler = AttributeHandler::$handler[$attr->getName()];
        $bean = BeanFactory::getBean($handler);
        if ($bean instanceof CustomAttributeInterface) {
            $bean->handle();
        }
    }
}
