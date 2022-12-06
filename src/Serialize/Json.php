<?php

namespace Zorro\Serialize;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Zorro\Serialize\Mapper\Mapper;
use Zorro\Serialize\Mapper\MapperInterface;
use Zorro\Serialize\Parser\JsonParser;
use Zorro\Serialize\Parser\ParserInterface;
use Zorro\Validation\Validator;

class Json extends SerializerAbstract
{
    /** @var MapperInterface */
    public static $mapper;

    /** @var ParserInterface */
    protected static $parser;

    /** @var self */
    protected static $serializer;

    public static function create()
    {
        if (self::$serializer === null) {
            $json = new self();
            $json::$parser = new JsonParser(new JsonEncoder());
            $json::$mapper = new Mapper();
            self::$serializer = $json;
        }
        return self::$serializer;
    }

    public static function Unmarshal(string $data, string $dest)
    {
        $decoded = self::create()::$parser->decode($data);
        return self::mapping($decoded, $dest);
    }

    public static function mapping($decoded, $dest)
    {
        $obj = self::create()::$mapper->Unmarsharl($decoded, $dest);
        Validator::validate($obj);
        return $obj;
    }

    public static function Marshal($data, array $context = []): string
    {
        return self::create()::$parser->encode($data);
    }
}
