<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/7
 * Time: 20:48
 */


namespace Zorro\Http\Server\Adapter\Swoole;

use Swoole\Http\Request as SwooleReq;
use Swoole\Http\Response as SwooleResp;
use Swoole\Http\Server;
use Zorro\Http\Request;
use Zorro\Http\Server\AdapterAbstract;
use Zorro\Sync\Pool;

class SwooleAdapter extends AdapterAbstract
{
    public function start(int $port = 8081, string $host = "0.0.0.0")
    {
        $server = new Server($host, $port);
        $requestPool = new Pool(function (): Request {
            return new Request();
        });
        $responsePool = new Pool(function (): Response {
            return new Response();
        });
        $server->on("request", function (SwooleReq $request, SwooleResp $response) use (&$requestPool, &$responsePool) {
            $req = $requestPool->get();
            /** @var Request $r */
            $r = $req->val;
            $r->header = $request->header;
            $r->query = $request->get;
            $r->post = $request->post;
            $r->request = $request->rawContent();
            $r->method = $request->server["request_method"];
            $r->uri = $request->server["request_uri"];;

            $resp = $responsePool->get();
            /** @var Response $rsp */
            $rsp = $resp->val;
            $rsp->response = $response;

            $this->zorro->serveHttp($r, $rsp);

            $requestPool->put($req);
            $responsePool->put($resp);
        });
        $server->on("start", function () {
            cli_set_process_title("zorro_master");
        });
        $server->on("workerStart", function (\Swoole\Server $server, int $workerId) {
            cli_set_process_title("zorro_worker" . $workerId);
        });
        $server->on("managerStart", function () {
            cli_set_process_title("zorro_manager");
        });
        $server->start();
    }
}
