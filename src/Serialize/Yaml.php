<?php

namespace Zorro\Serialize;

use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Zorro\Serialize\Mapper\Mapper;
use Zorro\Serialize\Mapper\MapperInterface;
use Zorro\Serialize\Parser\ParserInterface;
use Zorro\Serialize\Parser\YamlParser;
use Zorro\Validation\Validator;

class Yaml extends SerializerAbstract
{
    /** @var MapperInterface */
    public static $mapper;

    /** @var ParserInterface */
    protected static $parser;

    /** @var self */
    protected static $serializer;

    static function create()
    {
        if (self::$serializer === null) {
            $yaml = new self();
            Yaml::$parser = new YamlParser(new YamlEncoder());
            Yaml::$mapper = new Mapper();
            self::$serializer = $yaml;
        }
        return self::$serializer;
    }

    public static function Unmarshal(string $data, string $dest)
    {
        $decoded = self::create()::$parser->decode($data);
        $obj = self::create()::$mapper->Unmarsharl($decoded, $dest);
        Validator::validate($obj);
        return $obj;
    }

    public static function Marshal($data, array $context = []): string
    {
        return self::create()::$parser->encode($data);
    }
}
