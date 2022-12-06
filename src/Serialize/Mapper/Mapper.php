<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:04
 */


namespace Zorro\Serialize\Mapper;


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

}
