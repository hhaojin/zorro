<?php

namespace Zorro\Validation;

abstract class ValidatorAbstract
{
    protected $message;

    abstract function check(string $name, $value): bool;

    public function validate(string $name, $value):void
    {
        if ($this->check($name, $value)) {
            return;
        }
        if (!is_null($this->message)) {
            throw new ValidateException($this->message);
        }
        throw new ValidateException("{$name} validate failed");
    }
}
