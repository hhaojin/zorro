<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/7
 * Time: 20:34
 */

namespace Zorro\Http\Server;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as Req;
use Workerman\Worker;
use Zorro\Http\Request;
use Zorro\Http\Response;

class WorkerManAdapter extends AdapterAbstract
{

    public function start(int $port = 8081, string $host = "0.0.0.0")
    {
        $addr = sprintf("http://%s:%d", $host, $port);
        {
            $worker = new Worker($addr);
            $worker->count = 4;
            $worker->onMessage = function (TcpConnection $connection, Req $request) {
                $req = new Request();
                $req->header = $request->header();
                $req->query = $request->get();
                $req->post = $request->post();
                $req->request = $request->rawBody();
                $req->method = $request->method();
                $req->uri = explode("?", $request->uri())[0];

                $response = new Response($connection);

                $this->zorro->serveHttp($req, $response);
            };
        }
        Worker::runAll();
    }
}