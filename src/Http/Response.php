<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:10
 */


namespace Zorro\Http;


class Response implements ResponseInterface
{
    /** @var \Swoole\Http\Response */
    protected $response;

    /** @var int */
    protected $statusCode = 0;

    /** @var array */
    protected $responseHeader = [];

    /** @var string */
    protected $body = "";

    public function __construct($response)
    {
        $this->response = $response;
    }

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

    public function end(): void
    {
        $this->response->status($this->getStatusCode());
        foreach ($this->responseHeader as $key => $value) {
            $this->response->header($key, $value);
        }
        if (strlen($this->body) > 0) {
            $this->response->write($this->body);
        }
        $this->response->end();
    }
}
