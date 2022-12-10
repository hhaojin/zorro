<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/7
 * Time: 20:34
 */

namespace Zorro\Http\Server\Adapter\Workerman;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as WorkerReq;
use Workerman\Worker;
use Zorro\Http\Request;
use Zorro\Http\Server\AdapterAbstract;
use Zorro\Sync\Pool;

class WorkerManAdapter extends AdapterAbstract
{

    public function start(int $port = 8081, string $host = "0.0.0.0")
    {
        $addr = sprintf("http://%s:%d", $host, $port);
        {
            $worker = new Worker($addr);
            $worker->count = 4;
            $requestPool = new Pool(function (): Request {
                return  new Request();
            });
            $responsePool = new Pool(function (): Response {
                return new Response();
            });
            $worker->onMessage = function (TcpConnection $connection, WorkerReq $request) use (&$requestPool, &$responsePool) {
                $req = $requestPool->get();
                /** @var Request $r */
                $r = $req->val;
                $r->header = $request->header();
                $r->query = $request->get();
                $r->post = $request->post();
                $r->request = $request->rawBody();
                $r->method = $request->method();
                $r->uri = explode("?", $request->uri())[0];

                $resp = $responsePool->get();
                /** @var Response $rsp */
                $rsp = $resp->val;
                $rsp->connection = $connection;

                $this->zorro->serveHttp($r, $rsp);
            };
        }
        Worker::runAll();
    }
}