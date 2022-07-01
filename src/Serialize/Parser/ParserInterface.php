<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:54
 */


namespace Zorro\Serialize\Parser;


interface ParserInterface
{
    /**
     * @param string $data
     * @param array $context
     * @return array
     */
    public function decode(string $data, array $context = []): array;

    /**
     * @param array|object $data
     * @return string
     */
    public function encode($data, array $context = []): string;

}
