<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/11
 * Time: 20:26
 */


namespace Zorro\Sync;

use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{

    public function testPut()
    {
        $p = new Pool(function () use (&$a) {
            return $a++;
        });
        $a = 1;
        $arr = [];
        for ($i = 0; $i < 10; $i++) {
            $node = $p->get();
            $arr[$i] = $node;
        }
        for ($i = 0; $i < 10; $i++) {
            $p->put($arr[$i]);
        }
        for ($i = 0; $i < 10; $i++) {
            $node = $p->get();
            $arr[$i] = $node;
        }
        for ($i = 0; $i < 10; $i++) {
            $p->put($arr[$i]);
        }
        for ($i = 0; $i < 20; $i++) {
            $node = $p->get();
            $arr[$i] = $node;
        }
        for ($i = 0; $i < 20; $i++) {
            $p->put($arr[$i]);
        }
        for ($i = 0; $i < 20; $i++) {
            $node = $p->get();
            $this->assertEquals($i + 1, $node->val);
        }

    }

    public function testGet()
    {
        $p = new Pool(function () use (&$a) {
            return $a++;
        });
        $a = 1;
        for ($i = 0; $i < 10; $i++) {
            $node = $p->get();
            $this->assertEquals($i + 1, $node->val);
        }
    }
}
