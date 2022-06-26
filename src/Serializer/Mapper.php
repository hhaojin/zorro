<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:04
 */


namespace Zorro\Serializer;


use ReflectionProperty;
use Zorro\Validation\ValidatorAbstract;

class Mapper extends \JsonMapper implements MapperInterface
{
    public $bEnforceMapType = false;

    protected $strict;

    /**
     * @param array|object[] $data
     * @param string $dest
     * @return object|object[]
     */
    public function Unmarsharl($data, string $dest)
    {
        if (count($data) === 0) {
            throw new MapperException("data cannot be null");
        }
        if (is_integer(array_key_first($data))) {
            return $this->mapArray($data, [], $dest);
        }
        if (class_exists($dest)) {
            return $this->map($data, new $dest());
        }
        throw new MapperException("class {$dest} not exists");
    }

    protected function setProperty($object, $accessor, $value)
    {
        if (!$accessor->isPublic() && $this->bIgnoreVisibility) {
            $accessor->setAccessible(true);
        }
        if ($accessor instanceof ReflectionProperty) {
            //handle validate, if pass then set value, else throw exception
            $this->validate($accessor, $value);
            $accessor->setValue($object, $value);
        } else {
            //setter method
            $accessor->invoke($object, $value);
        }
    }

    public function validate(ReflectionProperty $property, $value)
    {
        $name = $property->getName();
        foreach ($property->getAttributes() as $attribute) {
            $instace = $attribute->newInstance();
            if (!$instace instanceof ValidatorAbstract) {
                continue;
            }
            $instace->validate($name, $value);
        }
    }

}
