<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:04
 */


namespace Zorro\Serializer;


class Mapper extends \JsonMapper implements MapperInterface
{
    public $bEnforceMapType = false;

    /**
     * @param array|object[] $data
     * @param string $dest
     * @return object|object[]
     */
    public function Unmarsharl($data, string $dest)
    {
        if (is_array($data) && count($data) > 0 && is_array($data[0])) {
            return $this->mapArray($data, [], $dest);
        }
        if (class_exists($dest)) {
            return $this->map($data, new $dest());
        }
        throw new MapperException("class {$dest} not exists");
    }
}