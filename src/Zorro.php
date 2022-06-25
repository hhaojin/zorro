<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:03
 */

namespace Zorro;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class Zorro extends RouteGroup
{
    /** @var Dispatcher */
    protected $dispatcher;

    public function Run(int $port = 80, string $host = "0.0.0.0"): void
    {
        $this->initDispatcher();
        $server = new Server($host, $port);
        $server->on("request", [$this, "serveHttp"]);
        $server->start();
    }

    protected function initDispatcher(): void
    {
        $collector = new RouteCollector(new Std(), new GroupCountBased());
        $this->collectRouteGroup($collector, ["" => $this]);
        $this->dispatcher = new Dispatcher($collector->getData());
        $this->handles = null;
        $this->routeGroups = null;
        $this->routes = null;
    }

    protected function collectRouteGroup(RouteCollector $collector, array $groups): void
    {
        /**
         * @var string $groupName
         * @var RouteGroup $routeGroup
         */
        foreach ($groups as $groupName => $routeGroup) {
            $collector->addGroup($groupName, function (RouteCollector $r) use ($collector, $routeGroup) {
                foreach ($routeGroup->getRoutes() as $method => $routes) {
                    foreach ($routes as $path => $handle) {
                        $handles = $routeGroup->getHandles();
                        $handles[] = $handle;
                        $r->addRoute($method, $path, $handles);
                    }
                }
                $this->collectRouteGroup($collector, $routeGroup->getGroups());
            });
        }
    }

    public function serveHttp(Request $request, Response $response): void
    {
        $context = new Context($request, $response);
        try {
            $this->handleRequest($context);
            $context->response->status($context->getStatusCode());
            foreach ($context->getResponseHeader() as $key => $value) {
                $context->response->header($key, $value);
            }
            $context->response->end($context->getResponseBody());
        } catch (\Throwable $e) {

        }
    }

    public function dispatch(string $method, string $uri): array
    {
        return $this->dispatcher->dispatch($method, $uri);
    }

    protected function handleRequest(Context $ctx): void
    {
        $routeInfo = $this->dispatch($ctx->request->server["request_method"], $ctx->request->server["request_uri"]);
        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $ctx->setHandles($routeInfo[1]);
                $ctx->setParams($routeInfo[2]);
                $ctx->next();
                break;
            case Dispatcher::NOT_FOUND:
                $ctx->response->status(404);
                $ctx->response->end();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $ctx->response->status(405);
                $ctx->response->end();
                break;
        }
    }
}
