<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/10
 * Time: 23:59
 */

namespace Zorro\Http\Server\Adapter\Workerman;

use Workerman\Connection\TcpConnection;
use Zorro\Http\ResponseInterface;
use Zorro\Http\Server\ResponseAbstract;

class Response extends ResponseAbstract implements ResponseInterface
{
    /** @var TcpConnection */
    public $connection;

    public function end(): void
    {
        $resp = new \Workerman\Protocols\Http\Response(
            $this->getStatusCode(), $this->responseHeader, $this->body);

        $this->connection->send($resp);
        $this->body = "";
        $this->responseHeader = [];
        $this->statusCode = 0;
    }
}
