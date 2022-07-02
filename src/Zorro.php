<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:03
 */

namespace Zorro;

use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Zorro\Attribute\Collector\AttributeCollector;
use Zorro\Http\Request as HttpRequest;
use Zorro\Http\Response as HttpResponse;
use Zorro\Serialize\Serializer;

class Zorro extends RouteGroup
{
    /** @var Dispatcher */
    protected $dispatcher;

    public function __construct()
    {
        BeanFactory::_init();
        Serializer::init();
    }

    public function Run(int $port = 80, string $host = "0.0.0.0"): void
    {
        $this->initDispatcher();
        $server = new Server($host, $port);
        $server->on("request", [$this, "serveHttp"]);
        $server->on("start", function () { cli_set_process_title("zorro_master"); });
        $server->on("workerStart", function (\Swoole\Server $server, int $workerId) { cli_set_process_title("zorro_worker" . $workerId); });
        $server->on("managerStart", function () { cli_set_process_title("zorro_manager"); });
        $server->start();
    }

    public function initDispatcher(): void
    {
        $handles = $this->handles();
        $this->printHandles($handles);
        $this->dispatcher = new Dispatcher($handles);
    }

    private function printHandles(array $handles)
    {
        list($staticRoute, $variableRouteData) = $handles;
        foreach ($staticRoute as $method => $handle) {
            echo $method . "   -->   " . array_key_first($handle) . PHP_EOL;
        }
        foreach ($variableRouteData as $method => $handle) {
            foreach ($handle as $uris) {
                echo $method . "   -->   " . $uris["regex"] . PHP_EOL;
            }
        }
    }

    public function scanDir(array $dirs, array $namespaces): void
    {
        FileLoader::loadDirFiles(...$dirs);
        $classes = get_declared_classes();
        AttributeCollector::collectAttributes($classes, $namespaces);
    }

    public function serveHttp(Request $request, Response $response): void
    {
        $context = new Context(new HttpRequest($request), new HttpResponse($response));
        $this->handleRequest($context);
    }

    public function dispatch(string $method, string $uri): array
    {
        return $this->dispatcher->dispatch($method, $uri);
    }

    protected function handleRequest(Context $ctx): void
    {
        $routeInfo = $this->dispatch($ctx->getMethod(), $ctx->getUri());
        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $ctx->setHandles($routeInfo[1]);
                $ctx->setParams($routeInfo[2]);
                $ctx->next();
                break;
            case Dispatcher::NOT_FOUND:
                $ctx->status(404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $ctx->status(405);
                break;
        }
        $ctx->end();
    }
}
