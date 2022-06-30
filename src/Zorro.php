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
use Throwable;
use Zorro\Http\Request as HttpRequest;
use Zorro\Http\Response as HttpResponse;
use Zorro\Serializer\Serializer;
use Zorro\Serializer\SerializerInterface;

class Zorro extends RouteGroup
{
    /** @var Dispatcher */
    protected $dispatcher;

    /** @var SerializerInterface */
    protected $serializer;

    protected $scanDirs = [];

    public function __construct()
    {
        $this->serializer = Serializer::default();
    }

    public function Run(int $port = 80, string $host = "0.0.0.0"): void
    {
        $this->initDispatcher();
        $server = new Server($host, $port);
        $server->on("request", [$this, "serveHttp"]);
        $server->start();
    }

    public function initDispatcher(): void
    {
        $this->dispatcher = new Dispatcher($this->handles());
    }

    public function scanDir(string ...$dirs): void
    {
        $this->scanDirs = $dirs;
    }

    public function collectAttribute()
    {
        FileLoader::loadDirFiles(...$this->scanDirs);
        $classes = get_declared_classes();
        foreach ($classes as $class) {

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
