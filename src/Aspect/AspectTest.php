<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/22
 * Time: 22:56
 */

namespace Zorro\Aspect;

use PHPUnit\Framework\TestCase;

class AspectTest extends TestCase
{
    public function testCall(): void
    {
        $f1 = function (JoinPoint $c, $args) {
            $c->process($args);
        };
        $f2 = function (JoinPoint $c, $args) {
            $c->process($args);
        };
        $f3 = function ($a, $b, $c) {

        };
        $proxy = new Aspect("aa", $f1, $f2, $f3);
        $this->assertEquals(null, $proxy->aa(1, 2, 4));
    }

    public function testCall2(): void
    {
        $th = new class {
            public function test(JoinPoint $ctx, array $args): void
            {
                var_dump("f1_>");
                $args[0]++;
                $ret = $ctx->process($args);
                var_dump("f1_>{$ret}");
            }
        };
        $rf = new \ReflectionClass($th);
        $m = $rf->getMethod("test");
        $f1 = $m->getClosure($th);

        $f2 = function (JoinPoint $c, $args) {
            var_dump("f2_>");
        };
        $f3 = function ($a, $b, $c) {
            var_dump("f3");
            return $a + $b + $c;
        };
        $proxy = new Aspect("aa", $f1, $f2, $f3);
        $this->assertEquals(8, $proxy->aa(1, 2, 4));
    }
}

