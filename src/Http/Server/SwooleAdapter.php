<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/7
 * Time: 20:48
 */


namespace Zorro\Http\Server;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class SwooleAdapter extends AdapterAbstract
{
    public function start(int $port = 8081, string $host = "0.0.0.0")
    {
        $server = new Server($host, $port);
        $server->on("request", function (Request $request, Response $response) {
            $req = new \Zorro\Http\Request();
            $req->header = $request->header;
            $req->query = $request->get;
            $req->post = $request->post;
            $req->request = $request->rawContent();
            $req->method = $request->server["request_method"];
            $req->uri = $request->server["request_uri"];;

            $resp = new \Zorro\Http\Response($response);
            $this->zorro->serveHttp($req, $resp);
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
