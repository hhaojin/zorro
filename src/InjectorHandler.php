<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/10
 * Time: 20:48
 */


namespace Zorro;

use ReflectionAttribute;
use ReflectionProperty;
use Zorro\Attribute\CustomAttributeInterface;

class InjectorHandler implements CustomAttributeInterface
{
    /**
     * @param ReflectionProperty $reflect
     * @param object $instance
     * @param ReflectionAttribute $attribute
     * @return \Closure|void
     */
    public function handle($reflect, $instance, $attribute)
    {
        $attrInstance = $attribute->newInstance();
        $rf = new \ReflectionClass($attrInstance);
        $property = $rf->getProperty("bean");
        $property->setAccessible(true);
        $bean = $property->getValue($attrInstance);

        $reflect->setAccessible(true);
        $reflect->setValue($instance, BeanFactory::getBean($bean));
    }
}