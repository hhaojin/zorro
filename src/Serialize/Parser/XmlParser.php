<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:56
 */


namespace Zorro\Serialize\Parser;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlParser implements ParserInterface
{
    const type = 'xml';

    /** @var XmlEncoder */
    protected $encoder;

    public function __construct(XmlEncoder $encoder)
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
