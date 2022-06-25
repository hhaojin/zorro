<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 22:06
 */


namespace Zorro;

use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;

class ZorroTest extends TestCase
{

    public function testDispatch()
    {
        $zorror = new \Zorro\Zorro();
        $zorror->Get("/test", function (Context $ctx) {
            $ctx->setResponseBody("test");
        });

        $mdw = new class implements HandleInterface {
            public function handle(Context $context)
            {
                $context->next();
                $res = $context->getResponseBody();
                $context->setResponseBody($res . "xx");
            }
        };

        $v1Group = $zorror->Group("/v1", $mdw);
        $v1Group->Use($mdw);
        {
            $v1Group->Get("/test", function (Context $ctx) {
                $ctx->setResponseBody("v1/test");
            });
            $v1test = $v1Group->Group("/test");
            {
                $v1test->Get("/xxx", function (Context $ctx) {
                    $ctx->setResponseBody("v1/test/xxx");
                });
            }
        }
        $v2Group = $zorror->Group("/v2");
        {
            $v2Group->Post("/fff", function (Context $ctx) {
                $ctx->setResponseBody("v2/fff");
            });
        }
        $rf = new \ReflectionClass($zorror);
        $m = $rf->getMethod("initDispatcher");
        $m->setAccessible(true);
        $m->invoke($zorror);

        $m = $rf->getMethod("dispatch");
        $m->setAccessible(true);


        $ctx = new Context(null, null);
        $routeInfo = $m->invoke($zorror, "GET", "/test");
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->setHandles($routeInfo[1]);
        $ctx->next();
        $this->assertEquals("test", $ctx->getResponseBody());

        $ctx = new Context(null, null);
        $routeInfo = $m->invoke($zorror, "GET", "/v1/test");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->next();
        $this->assertEquals("v1/testxxxx", $ctx->getResponseBody());

        $ctx = new Context(null, null);
        $routeInfo = $m->invoke($zorror, "GET", "/v1/test/xxx");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->next();
        $this->assertEquals("v1/test/xxxxxxx", $ctx->getResponseBody());

        $ctx = new Context(null, null);
        $routeInfo = $m->invoke($zorror, "POST", "/v2/fff");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals($routeInfo[0], Dispatcher::FOUND);
        $ctx->next();
        $this->assertEquals("v2/fff", $ctx->getResponseBody());

        $routeInfo = $m->invoke($zorror, "GET", "/v2/fff");
        $this->assertEquals(Dispatcher::METHOD_NOT_ALLOWED, $routeInfo[0]);

        $routeInfo = $m->invoke($zorror, "GET", "/v2/test/fffxx");
        $this->assertEquals(Dispatcher::NOT_FOUND, $routeInfo[0]);
    }

}
