<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/5
 * Time: 21:45
 */


namespace Zorro\Validation;

use Zorro\Attribute\AttributeCollector;

class Validator
{
    /**
     * @var array $ruleCache
     * example:
     * [
     *      className => [
     *          propertyName => [
     *              tag1 => [],
     *              tag2 => [],
     *          ]
     *      ]
     * ]
     */
    protected static $ruleCache = [];

    /** @var \Respect\Validation\Validator */
    protected static $respect;

    protected static $validator;

    protected static $customRules = [];

    public static function create()
    {
        if (self::$validator === null) {
            self::$respect = new \Respect\Validation\Validator();
            self::$validator = new self();
        }
        return self::$validator;
    }


    /**
     * @param Object $input
     */
    public static function validate($input): void
    {
        if (is_array($input)) {
            foreach ($input as $in) {
                self::create()->check($in);
            }
        } else {
            self::create()->check($input);
        }
    }

    private function check($input)
    {
        $rf = new \ReflectionClass($input);
        $classRules = $this->getRuleCache($rf, $input);
        if ($classRules === null) {
            return;
        }
        foreach ($classRules as $propertyName => $rules) {
            foreach ($rules[0] as $ruleName => $value) {
                $fieldValue = $rf->getProperty($propertyName)->getValue($input);
                if (array_key_exists($ruleName, self::$customRules)) {
                    $ret = self::$customRules[$ruleName]($fieldValue, ...$value);
                } else {
                    $ret = self::$respect->__call($ruleName, $value)->validate($fieldValue);
                }
                if (!$ret) {
                    $notice = $value[1] !== "" ? $rules[1] : sprintf("invalid argument, property=%s, tag=%s", $propertyName, $ruleName);
                    throw new ValidateException($notice);
                }
            }
        }
    }

    private function getRuleCache(\ReflectionClass $rf, object $bean)
    {
        $name = $rf->getName();
        if (array_key_exists($name, self::$ruleCache)) {
            return self::$ruleCache[$name];
        }
        AttributeCollector::collectAttribute($rf, $bean);
        if (array_key_exists($name, self::$ruleCache)) {
            return self::$ruleCache[$name];
        }
        self::$ruleCache[$name] = null;
        return null;
    }

    public static function addCache(string $className, string $propertyName, array $tags, string $notice)
    {
        self::$ruleCache[$className][$propertyName] = [$tags, $notice];
    }

    public static function registerValidation(CustomTagAbstract $tag)
    {
        $rf = new \ReflectionClass($tag);
        $property = $rf->getProperty("tag");
        $property->setAccessible(true);
        $tagName = $property->getValue($tag);
        if (array_key_exists($tagName, self::$customRules)) {
            throw new \Exception(sprintf("%s is already declared", $tagName));
        }
        if ($tagName === "") {
            throw new \Exception(sprintf("must declare tag name"));
        }
        $property = $rf->getProperty("msg");
        $property->setAccessible(true);
        $msg = $property->getValue($tag);
        self::$customRules[$tagName] = [$rf->getMethod("validate")->getClosure($tag), $msg];
    }

}
