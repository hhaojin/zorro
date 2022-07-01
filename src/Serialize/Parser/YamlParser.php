<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:56
 */


namespace Zorro\Serialize\Parser;

use Symfony\Component\Serializer\Encoder\YamlEncoder;

class YamlParser implements ParserInterface
{
    const type = 'yaml';

    /** @var YamlEncoder */
    protected $encoder;

    public function __construct(YamlEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function decode(string $data, array $context = []): array
    {
        return $this->encoder->decode($data, self::type, $context);
    }

    public function encode($data, array $context = []): string
    {
        return $this->encoder->encode($data, self::type, $context);
    }

}
