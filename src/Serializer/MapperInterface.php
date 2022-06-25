<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:01
 */


namespace Zorro\Serializer;


interface MapperInterface
{
    /**
     * @param array|object[] $data
     * @param string $dest
     * @return object|object[]
     */
    public function Unmarsharl($data, string $dest);
}
