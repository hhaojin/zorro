<?php

namespace Zorro\Validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Eq extends ValidatorAbstract
{
    protected $value;

    public function __construct($value, string $message = null)
    {
        $this->value = $value;
        $this->message = $message;
    }

    function check($name, $value): bool
    {
        return $this->value === $value;
    }
}