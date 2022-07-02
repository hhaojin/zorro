<?php

namespace Zorro\Attribute\Collector;

use ReflectionProperty;

class PropertiesAttributeCollector extends AbstractCollector
{
    public function collect(ReflectionProperty $rf)
    {
        $this->handleAttrs($rf->getAttributes());
    }
}