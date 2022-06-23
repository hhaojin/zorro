<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/22
 * Time: 22:56
 */

namespace Zorro;

use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    public function testCall(): void
    {
        $f1 = function (InjectContext $c, $args) {
            $c->next($args);
        };
        $f2 = function (InjectContext $c, $args) {
            $c->next($args);
        };
        $f3 = function ($a, $b, $c) {

        };
        $proxy = new Proxy("aa", [$f1, $f2, $f3]);
        $this->assertEquals(null, $proxy->aa(1, 2, 4));
    }

    public function testCall2(): void
    {
        $th = new class {
            public function test(InjectContext $ctx, array $args): void
            {
                $args[0]++;
                $ctx->next($args);
            }
        };
        $rf = new \ReflectionClass($th);
        $m = $rf->getMethod("test");
        $m->getClosure($th);
        $f1 = $m->getClosure($th);

        $f2 = function (InjectContext $c, $args) {
            $c->next($args);
        };
        $f3 = function ($a, $b, $c) {
            return $a + $b + $c;
        };
        $proxy = new Proxy("aa", [$f1, $f2, $f3]);
        $this->assertEquals(8, $proxy->aa(1, 2, 4));
    }
}

