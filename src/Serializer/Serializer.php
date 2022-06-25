<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 21:29
 */


namespace Zorro\Serializer;

class Serializer
{
    /** @var MapperInterface */
    private $mapper;

    /** @var ParserInterface */
    private $parser;

    public function __construct(MapperInterface $mapper, ParserInterface $parser)
    {
        $this->mapper = $mapper;
        $this->parser = $parser;
    }

    public function getParser(): ParserInterface
    {
        return $this->parser;
    }

    public function getMapper(): MapperInterface
    {
        return $this->mapper;
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

}
