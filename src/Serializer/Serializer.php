<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 21:29
 */


namespace Zorro\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;

class Serializer implements SerializerInterface
{
    /** @var MapperInterface */
    private $mapper;

    /** @var ParserInterface */
    private $parser;

    public static function default(): SerializerInterface
    {
        $parser = new Parser([], [new XmlEncoder(), new JsonEncoder(), new YamlEncoder()]);
        $mapper = new Mapper();
        $serializer = new self($mapper, $parser);
        return $serializer;
    }

    public function __construct(MapperInterface $mapper, ParserInterface $parser)
    {
        $this->mapper = $mapper;
        $this->parser = $parser;
    }

    public function jsonUnmarshal(string $data, string $dest)
    {
        $decoded = $this->parser->decodeJson($data);
        return $this->mapper->Unmarsharl($decoded, $dest);
    }

    public function jsonMarshal($data): string
    {
        return $this->parser->encodeJson($data);
    }

    public function xmlUnmarshal(string $data, string $dest)
    {
        $decoded = $this->parser->decodeXml($data);
        return $this->mapper->Unmarsharl($decoded, $dest);
    }

    public function xmlMarshal($data): string
    {
        return $this->parser->encodeXml($data);
    }

    public function yamlUnmarshal(string $data, string $dest)
    {
        $decoded = $this->parser->decodeYaml($data);
        return $this->mapper->Unmarsharl($decoded, $dest);
    }

    public function yamlMarshal($data): string
    {
        return $this->parser->encodeYaml($data);
    }

    public function Unmarsharl(array $data, string $dest)
    {
        return $this->mapper->Unmarsharl($data, $dest);
    }
}
