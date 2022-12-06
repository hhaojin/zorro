<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/6
 * Time: 21:15
 */


namespace Zorro\Serialize;


abstract class SerializerAbstract implements SerializerInterface
{
    abstract static function create();
}