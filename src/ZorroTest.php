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
            return "test";
        });

        $v1Group = $zorror->Group("/v1");
        {
            $v1Group->Get("/test", function (Context $ctx) {
                return "v1/test";
            });
            $v1test = $v1Group->Group("/test");
            {
                $v1test->Get("/xxx", function (Context $ctx) {
                    return "v1/test/xxx";
                });
            }
        }
        $v2Group = $zorror->Group("/v2");
        {
            $v2Group->Post("/fff", function (Context $ctx) {
                return "v2/fff";
            });
        }
        $rf = new \ReflectionClass($zorror);
        $m = $rf->getMethod("initDispatcher");
        $m->setAccessible(true);
        $m->invoke($zorror);

        $m = $rf->getMethod("dispatch");
        $m->setAccessible(true);
        $routeInfo = $m->invoke($zorror, "GET", "/test");
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx = new Context(null, null);
        $this->assertEquals("test", $routeInfo[1]($ctx));

        $routeInfo = $m->invoke($zorror, "GET", "/v1/test");
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals("v1/test", $routeInfo[1]($ctx));

        $routeInfo = $m->invoke($zorror, "GET", "/v1/test/xxx");
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals("v1/test/xxx", $routeInfo[1]($ctx));

        $routeInfo = $m->invoke($zorror, "POST", "/v2/fff");
        $this->assertEquals($routeInfo[0], Dispatcher::FOUND);
        $this->assertEquals("v2/fff", $routeInfo[1]($ctx));

        $routeInfo = $m->invoke($zorror, "GET", "/v2/fff");
        $this->assertEquals(Dispatcher::METHOD_NOT_ALLOWED, $routeInfo[0]);

        $routeInfo = $m->invoke($zorror, "GET", "/v2/test/fffxx");
        $this->assertEquals(Dispatcher::NOT_FOUND, $routeInfo[0]);
    }

}
