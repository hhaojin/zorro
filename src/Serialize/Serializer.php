<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 21:29
 */


namespace Zorro\Serialize;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Zorro\Serialize\Mapper\Mapper;
use Zorro\Serialize\Parser\JsonParser;
use Zorro\Serialize\Parser\XmlParser;
use Zorro\Serialize\Parser\YamlParser;

class Serializer
{
    public static function init()
    {
        $mapper = new Mapper();

        Json::$parser = new JsonParser(new JsonEncoder());
        Json::$mapper = $mapper;
        Xml::$parser = new XmlParser(new XmlEncoder());
        Xml::$mapper = $mapper;
        Yaml::$parser = new YamlParser(new YamlEncoder());
        Yaml::$mapper = $mapper;
    }
}
