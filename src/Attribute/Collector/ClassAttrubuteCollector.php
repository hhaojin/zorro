<?php

namespace Zorro\Attribute\Collector;

use ReflectionClass;

class ClassAttrubuteCollector extends AbstractCollector
{
    public function collect(ReflectionClass $rf)
    {
        $this->handleAttrs($rf->getAttributes());
    }
}
