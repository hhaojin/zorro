<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/5
 * Time: 22:54
 */


namespace Zorro\Attribute\Collector;

use Attribute;
use ReflectionAttribute;
use Zorro\Attribute\CustomAttribute;

abstract class CollectorAbstract
{
    protected static function shouldHandle(ReflectionAttribute $attr): bool
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

    /**
     * @param \ReflectionMethod|\ReflectionClass|\ReflectionProperty $rf
     * @param object $bean
     * @return void
     */
    abstract static function collect($rf, $bean): void;
}