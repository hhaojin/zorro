<?php

namespace Example\CustomAttribute;

use Zorro\Aspect\JoinPoint;
use Zorro\Attribute\CustomAttributeInterface;

class LogHandler implements CustomAttributeInterface
{
    public function handle($reflect, $instance, $attribute): \Closure
    {
        return function (JoinPoint $joinPoint, array $args) use ($reflect) {
            echo "before -> " . $reflect->getName() . PHP_EOL;
            $joinPoint->process($args);
            echo "after -> " . $reflect->getName() . PHP_EOL;
        };
    }
}
