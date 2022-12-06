<?php

namespace Example\CustomAttribute;

use Attribute;
use Zorro\Attribute\CustomAttribute;

#[Attribute(Attribute::TARGET_METHOD)]
#[CustomAttribute(LogHandler::class)]
class TestLog
{

}
