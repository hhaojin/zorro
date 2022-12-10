<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/11
 * Time: 0:00
 */


namespace Zorro\Http\Server;


class ResponseAbstract
{
    /** @var int */
    protected $statusCode = 0;

    /** @var array */
    protected $responseHeader = [];

    /** @var string */
    protected $body = "";

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeader(string $key): string
    {
        return $this->responseHeader[$key];
    }

    public function header(string $key, $value)
    {
        $this->responseHeader[$key] = $value;
    }

    public function status(int $code)
    {
        $this->statusCode = $code;
    }

    public function write(string $body): void
    {
        $this->body .= $body;
    }

}
