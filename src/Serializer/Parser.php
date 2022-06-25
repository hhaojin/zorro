<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:56
 */


namespace Zorro\Serializer;

use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Parser extends SymfonySerializer implements ParserInterface
{
    const DecodeJson = 'json';
    const DecodeXml = 'xml';
    const DecodeYaml = 'yaml';

    public function decodeJson(string $data, array $context = []): array
    {
        return $this->decode($data, self::DecodeJson, $context);
    }

    public function encodeJson($data): string
    {
        return $this->encode($data, self::DecodeJson);
    }

    public function decodeXml(string $data, array $context = []): array
    {
        return $this->decode($data, self::DecodeXml, $context);
    }

    public function encodeXml($data): string
    {
        return $this->encode($data, self::DecodeXml);
    }

    public function decodeYaml(string $data, array $context = []): array
    {
        return $this->decode($data, self::DecodeYaml, $context);
    }

    public function encodeYaml($data): string
    {
        return $this->encode($data, self::DecodeYaml);
    }

}
