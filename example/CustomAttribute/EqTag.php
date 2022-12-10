<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/10
 * Time: 21:33
 */


namespace Example\CustomAttribute;

use Zorro\Validation\CustomTagAbstract;

class EqTag extends CustomTagAbstract
{
    protected $tag = "eq";

    public function validate($input, $value): bool
    {
        var_dump($input,$value);
        if ($input == $value) {
            return true;
        }
        return false;
    }
}