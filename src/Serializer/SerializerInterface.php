<?php

namespace Zorro\Serializer;

interface SerializerInterface extends MapperInterface
{
    public function jsonUnmarshal(string $data, string $dest);

    public function jsonMarshal($data): string;

    public function xmlUnmarshal(string $data, string $dest);

    public function xmlMarshal($data): string;

    public function yamlUnmarshal(string $data, string $dest);

    public function yamlMarshal($data): string;
}