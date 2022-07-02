<?php

namespace Zorro\Attribute\Collector;

class AttributeCollector
{
    public static function collectAttribute(array $namespaces)
    {
        $classCollector = new ClassAttrubuteCollector();
        $propertyCollector = new PropertiesAttributeCollector();
        $methodCollector = new MethodAttributeCollector();
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (!self::shouldCollect($class, $namespaces)) {
                continue;
            }
            $rf = new \ReflectionClass($class);
            $classCollector->collect($rf);

            $properties = $rf->getProperties();
            foreach ($properties as $property) {
                $propertyCollector->collect($property);
            }
            $methods = $rf->getMethods();
            foreach ($methods as $method) {
                $methodCollector->collect($method);
            }
        }
    }

    public static function shouldCollect($class, $namespaces): bool
    {
        foreach ($namespaces as $namespace) {
            if (strstr($class, $namespace)) {
                return true;
            }
        }
        return false;
    }
}
