<?php

namespace Example\CustomAttribute;

use Attribute;
use Zorro\Attribute\CustomAttribute;

#[Attribute]
#[CustomAttribute(LogHandler::class)]
class TestLog
{

}
