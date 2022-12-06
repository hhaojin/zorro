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
            foreach ($rules as $ruleName => $value) {
                $ret = self::$respect->__call($ruleName, $value)
                    ->validate($rf->getProperty($propertyName)->getValue($input));
                if (!$ret) {
                    throw new ValidateException(
                        sprintf("invalid argument, property=%s, tag=%s", $propertyName, $ruleName));
                }
            }
        }
    }

    private function getRuleCache(\ReflectionClass $rf, object $bean)
    {
        $name = $rf->getName();
        if (isset(self::$ruleCache[$name])) {
            return self::$ruleCache[$name];
        }
        AttributeCollector::collectAttribute($rf, $bean);
        if (isset(self::$ruleCache[$name])) {
            return self::$ruleCache[$name];
        }
        self::$ruleCache[$name] = null;
        return null;
    }

    public static function addCache(string $className, string $propertyName, array $tags)
    {
        self::$ruleCache[$className][$propertyName] = $tags;
    }
}
