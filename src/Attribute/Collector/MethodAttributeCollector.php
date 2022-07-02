<?php

namespace Zorro\Attribute\Collector;

use ReflectionMethod;

class MethodAttributeCollector extends AbstractCollector
{
    public function collect(ReflectionMethod $rf)
    {
        $this->handleAttrs($rf->getAttributes());
    }
}