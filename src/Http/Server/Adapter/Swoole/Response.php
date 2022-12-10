<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/11
 * Time: 0:04
 */

namespace Zorro\Http\Server\Adapter\Swoole;

use Zorro\Http\ResponseInterface;
use Zorro\Http\Server\ResponseAbstract;

class Response extends ResponseAbstract implements ResponseInterface
{
    /** @var \Swoole\Http\Response */
    public $response;

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

        $this->body = "";
        $this->responseHeader = [];
        $this->statusCode = 0;
    }
}