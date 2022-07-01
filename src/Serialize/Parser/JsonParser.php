<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:56
 */


namespace Zorro\Serialize\Parser;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonParser implements ParserInterface
{
    const type = 'json';

    /** @var JsonEncoder */
    protected $encoder;

    public function __construct(JsonEncoder $encoder)
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
