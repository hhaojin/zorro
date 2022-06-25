<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/25
 * Time: 22:54
 */


namespace Zorro\Serializer;


interface ParserInterface
{
    /**
     * @param string $data
     * @param array $context
     * @return array|object[]
     */
    public function decodeJson(string $data, array $context = []): array;

    /**
     * @param array|object $data
     * @return string
     */
    public function encodeJson($data): string;

    /**
     * @param string $data
     * @param array $context
     * @return array|object[]
     */
    public function decodeXml(string $data, array $context = []);

    /**
     * @param array|object $data
     * @return string
     */
    public function encodeXml($data): string;

    /**
     * @param string $data
     * @param array $context
     * @return array|object[]
     */
    public function decodeYaml(string $data, array $context = []);

    /**
     * @param array|object $data
     * @return string
     */
    public function encodeYaml($data): string;

}
