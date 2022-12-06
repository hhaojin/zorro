<?php

namespace Zorro\Attribute;

#[\Attribute]
class CustomAttribute
{
    public $handle;

    public function __construct($handle)
    {
        $this->handle = $handle;
    }
}