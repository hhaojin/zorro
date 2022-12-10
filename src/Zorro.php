<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:03
 */

namespace Zorro;

use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use Zorro\Http\Request;
use Zorro\Http\ResponseInterface;
use Zorro\Http\Server\Adapter\Adapter;
use Zorro\Sync\Pool;

class Zorro extends RouteGroup
{
    /** @var Dispatcher */
    protected $dispatcher;

    /** @var Pool */
    protected $pool;

    public function __construct()
    {
        $this->pool = new Pool(function (): Context {
            $ctx = new Context();
            return $ctx;
        });
    }

    public function Run(int $port = 8081, string $host = "0.0.0.0"): void
    {
        $this->echoLogo();
        $this->initDispatcher();
        Adapter::Serve($this)->start($port, $host);
    }

    protected function initDispatcher(): void
    {
        $handles = $this->handles();
        $this->printHandles($handles);
        $this->dispatcher = new Dispatcher($handles);
    }

    private function printHandles(array $handles)
    {
        $release = getopt("release", ["release:"]);
        if ($release) {
            return;
        }
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

    public function serveHttp(Request $request, ResponseInterface $response): void
    {
        $node = $this->pool->get();
        /** @var Context $context */
        $context = $node->val;
        $context->reset();
        $context->request = $request;
        $context->response = $response;

        $this->handleRequest($context);
        $this->pool->put($node);
    }

    protected function dispatch(string $method, string $uri): array
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

    protected function echoLogo()
    {
        echo <<<LOGO
                                                     __----~~~~~~~~~~~------___
                                    .  .   ~~//====......          __--~ ~~
                    -.            \_|//     |||\\  ~~~~~~::::... /~
                 ___-==_       _-~o~  \/    |||  \\            _/~~-
         __---~~~.==~||\=_    -_--~/_-~|-   |\\   \\        _/~
     _-~~     .=~    |  \\-_    '-~7  /-   /  ||    \      /
   .~       .~       |   \\ -_    /  /-   /   ||      \   /
  /  ____  /         |     \\ ~-_/  /|- _/   .||       \ /
  |~~    ~~|--~~~~--_ \     ~==-/   | \~--===~~        .\
           '         ~-|      /|    |-~\~~       __--~~
                       |-~~-_/ |    |   ~\_   _-~            /\
                            /  \     \__   \/~                \__
                        _--~ _/ | .-~~____--~-/                  ~~==.
                       ((->/~   '.|||' -_|    ~~-/ ,              . _||
                                  -_     ~\      ~~---l__i__i__i--~~_/
                                  _-~-__   ~)  \--______________--~~
                                //.-~~~-~_--~- |-------~~~~~~~~
                                       //.-~~~--\
                                神兽保佑
                               代码无BUG!
LOGO;
        echo PHP_EOL;
    }
}
