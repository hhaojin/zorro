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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Throwable;
use Zorro\Http\Request as HttpRequest;
use Zorro\Http\Response as HttpResponse;
use Zorro\Serializer\Mapper;
use Zorro\Serializer\Parser;
use Zorro\Serializer\Serializer;

class Zorro extends RouteGroup
{
    /** @var Dispatcher */
    protected $dispatcher;

    protected $serializer;

    public function __construct()
    {
        $parser = new Parser([], [new XmlEncoder(), new JsonEncoder(), new YamlEncoder()]);
        $mapper = new Mapper();
        $serializer = new Serializer($mapper, $parser);
        $this->serializer = $serializer;
    }

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
        $context = new Context(new HttpRequest($request), new HttpResponse($response));
        try {
            $this->handleRequest($context);
        } catch (Throwable $e) {
            var_dump($e->getMessage());
        }
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
                $ctx->setSerializer($this->serializer);
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
