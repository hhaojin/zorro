<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 23:01
 */


namespace Zorro\Serialize\Mapper;


interface MapperInterface
{
    /**
     * @param array $data
     * @param string $dest
     * @return object|object[]
     */
    public function Unmarsharl(array $data, string $dest);
}
