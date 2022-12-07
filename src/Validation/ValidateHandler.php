<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/5
 * Time: 23:05
 */


namespace Zorro\Validation;

use ReflectionAttribute;
use ReflectionProperty;
use Zorro\Attribute\CustomAttributeInterface;

class ValidateHandler implements CustomAttributeInterface
{
    /**
     * @param ReflectionProperty $reflect
     * @param object $instance
     * @param ReflectionAttribute $attribute
     * @return \Closure|void
     */
    public function handle($reflect, $instance, $attribute)
    {
        /** @var $attrInstance Validate */
        $attrInstance = $attribute->newInstance();
        $rf = new \ReflectionClass($attrInstance);
        $property = $rf->getProperty("tag");
        $property->setAccessible(true);
        $tag = $property->getValue($attrInstance);
        $tags = explode(";", $tag);

        $rules = [];
        foreach ($tags as $t) {
            $arr = explode("=", $t);
            if (count($arr) !== 2) {
                continue;
            }
            $rules[$arr[0]] = explode(",", $arr[1]);
        }
        if (count($rules) === 0) {
            return;
        }
        $className = get_class($instance);
        $propertyName = $reflect->getName();
        Validator::addCache($className, $propertyName, $rules);
    }
}
