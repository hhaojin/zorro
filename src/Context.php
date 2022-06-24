<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 21:15
 */

namespace Zorro;

use Swoole\Http\Request;
use Swoole\Http\Response;

class Context
{
    /** @var Request */
    public $request;

    /** @var Response */
    public $response;

    protected $params = [];

    protected $handles = [];

    protected $index = -1;

    protected $dispatcher;

    public function __construct($req, $resp)
    {
        $this->request = $req;
        $this->response = $resp;
    }

    public function query()
    {

    }

    public function bindJson()
    {

    }

    public function bindQuery()
    {

    }

    public function json(int $code, $body)
    {
        $this->response->status($code);
        $this->response->header("Content-Type", "application/json");
        $this->response->end(json_encode($body, true));
    }

    public function next(): void
    {
        $this->index++;
        while ($this->index < count($this->handles)) {
            $this->handles[$this->index]($this);
            $this->index++;
        }
    }

    public function setHandles(array $handles): void
    {
        $this->handles = $handles;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return false;
    }
}