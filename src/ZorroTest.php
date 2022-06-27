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
use Zorro\Http\RequsetInterface;
use Zorro\Http\ResponseInterface;

class MockRequest implements RequsetInterface
{

    public function getHeader(string $key): string
    {
        return "";
    }

    public function getQuery(string $key): string
    {
        return "";
    }

    public function getQuerys(): array
    {
        return [];
    }

    public function postForm(string $key): string
    {
        return "";
    }

    public function postForms(): array
    {
        return [];
    }

    public function getRawContent(): string
    {
        return "";
    }

    public function getMethod(): string
    {
        return "";
    }

    public function getUri(): string
    {
        return "";
    }
}

class MockResponse implements ResponseInterface
{

    protected $body = "";

    public function header(string $key, $value)
    {
    }

    public function getHeader(string $key): string
    {
        return "";
    }

    public function status(int $code)
    {

    }

    public function getStatusCode(): int
    {
        return 0;
    }

    public function write(string $body): void
    {
        $this->body .= $body;
    }

    public function end(): void
    {

    }
}

class ZorroTest extends TestCase
{

    public function testDispatch()
    {
        $zorror = new \Zorro\Zorro();
        $zorror->Get("/test", function (Context $ctx) {
            $ctx->response->write("test");
        });

        $mdw = new class implements HandleInterface {
            public function handle(Context $ctx)
            {
                $ctx->next();
                $ctx->response->write("xx");
            }
        };

        $v1Group = $zorror->Group("/v1", $mdw);
        $v1Group->Use($mdw);
        {
            $v1Group->Get("/test", function (Context $ctx) {
                $ctx->response->write("v1/test");
            });
            $v1test = $v1Group->Group("/test");
            {
                $v1test->Get("/xxx", function (Context $ctx) {
                    $ctx->response->write("v1/test/xxx");
                });
            }
        }
        $v2Group = $zorror->Group("/v2");
        {
            $v2Group->Post("/fff", function (Context $ctx) {
                $ctx->response->write("v2/fff");
            });
        }
        $rf = new \ReflectionClass($zorror);
        $m = $rf->getMethod("initDispatcher");
        $m->setAccessible(true);
        $m->invoke($zorror);

        $m = $rf->getMethod("dispatch");
        $m->setAccessible(true);

        $ctx = new Context(new MockRequest(), new MockResponse());
        $routeInfo = $m->invoke($zorror, "GET", "/test");
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->setHandles($routeInfo[1]);
        $ctx->next();
        $this->assertEquals("test", $this->getResponseValue($ctx->response));

        $ctx = new Context(new MockRequest(), new MockResponse());
        $routeInfo = $m->invoke($zorror, "GET", "/v1/test");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->next();
        $this->assertEquals("v1/testxxxx", $this->getResponseValue($ctx->response));

        $ctx = new Context(new MockRequest(), new MockResponse());
        $routeInfo = $m->invoke($zorror, "GET", "/v1/test/xxx");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->next();
        $this->assertEquals("v1/test/xxxxxxx", $this->getResponseValue($ctx->response));

        $ctx = new Context(new MockRequest(), new MockResponse());
        $routeInfo = $m->invoke($zorror, "POST", "/v2/fff");
        $ctx->setHandles($routeInfo[1]);
        $this->assertEquals(Dispatcher::FOUND, $routeInfo[0]);
        $ctx->next();
        $this->assertEquals("v2/fff", $this->getResponseValue($ctx->response));

        $routeInfo = $m->invoke($zorror, "GET", "/v2/fff");
        $this->assertEquals(Dispatcher::METHOD_NOT_ALLOWED, $routeInfo[0]);

        $routeInfo = $m->invoke($zorror, "GET", "/v2/test/fffxx");
        $this->assertEquals(Dispatcher::NOT_FOUND, $routeInfo[0]);
    }

    protected function getResponseValue(ResponseInterface $resp): string
    {
        $body = (new \ReflectionClass($resp))->getProperty("body");
        $body->setAccessible(true);
        return $body->getValue($resp);
    }

}
