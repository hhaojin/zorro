<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:10
 */


namespace Zorro\Http;

use Workerman\Connection\TcpConnection;

class Response implements ResponseInterface
{
    /** @var TcpConnection|\Swoole\Http\Response */
    public $response;

    /** @var int */
    protected $statusCode = 0;

    /** @var array */
    protected $responseHeader = [];

    /** @var string */
    protected $body = "";

    public function __construct($response)
    {
        $this->connect = $response;
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
        if ($this->connect instanceof TcpConnection) {
            $resp = new \Workerman\Protocols\Http\Response(
                $this->getStatusCode(), $this->responseHeader, $this->body);

            $this->connect->send($resp);
        } else {
            $this->connect->status($this->getStatusCode());
            foreach ($this->responseHeader as $key => $value) {
                $this->connect->header($key, $value);
            }
            if (strlen($this->body) > 0) {
                $this->connect->write($this->body);
            }
            $this->connect->end();
        }
        $this->body = "";
        $this->responseHeader = [];
        $this->statusCode = 0;
    }
}
