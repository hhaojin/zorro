<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/27
 * Time: 19:27
 */


namespace Zorro\Http;


interface ResponseInterface
{
    public function header(string $key, string $value);

    public function getHeader(string $key): string;

    public function status(int $code);

    public function getStatusCode(): int;

    public function write(string $body): void;

    public function end(): void;

}
