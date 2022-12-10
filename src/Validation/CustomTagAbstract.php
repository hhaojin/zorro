<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/10
 * Time: 21:37
 */


namespace Zorro\Validation;

abstract class CustomTagAbstract
{
    protected $tag = "";

    abstract function validate($input, $value): bool;
}
