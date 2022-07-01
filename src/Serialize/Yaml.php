<?php

namespace Zorro\Serialize;

use Zorro\Serialize\Mapper\MapperInterface;
use Zorro\Serialize\Parser\ParserInterface;

class Yaml implements SerializerInterface
{
    /** @var MapperInterface */
    public static $mapper;

    /** @var ParserInterface */
    public static $parser;

    public static function Unmarshal(string $data, string $dest)
    {
        $decoded = self::$parser->decode($data);
        return self::$mapper->Unmarsharl($decoded, $dest);
    }

    public static function Marshal($data, array $context = []): string
    {
        return self::$parser->encode($data);
    }
}