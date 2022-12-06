<?php

namespace Zorro\Attribute\Collector;

use ReflectionAttribute;
use Zorro\Attribute\CustomAttribute;

class AttributeHandler
{
    public static $handler = [

    ];

    public static function collectCustomAttribute(ReflectionAttribute $attr): void
    {
        $instanceRf = new \ReflectionClass($attr->newInstance());
        foreach ($instanceRf->getAttributes() as $attribute) {
            $attrInstance = $attribute->newInstance();
            if ($attrInstance instanceof CustomAttribute) {
                if (!isset(self::$handler[$attr->getName()])) {
                    self::$handler[$attr->getName()] = $attrInstance->handle;
                }
            }
        }
    }
}
