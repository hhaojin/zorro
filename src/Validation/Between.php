<?php

namespace Zorro\Validation;

use Attribute;
use Respect\Validation\Validator;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Between extends ValidatorAbstract
{
    protected $min;
    protected $max;

    public function __construct(int $min, int $max, string $message = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->message = $message;
    }

    function check(string $name, $value): bool
    {
        return Validator::between($this->min, $this->max)->validate($value);
    }
}