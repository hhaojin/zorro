<?php

namespace Zorro\Serialize;

interface SerializerInterface
{
    public static function Unmarshal(string $data, string $dest);

    public static function Marshal($data, array $context = []): string;

}