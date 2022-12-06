<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/5
 * Time: 22:11
 */


namespace Zorro\Validation;

use Attribute;
use Zorro\Attribute\CustomAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
#[CustomAttribute(ValidateHandler::class)]
class Validate
{
    protected $tag;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }
}