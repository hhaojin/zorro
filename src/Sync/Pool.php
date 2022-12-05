<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/11
 * Time: 19:42
 */

namespace Zorro\Sync;

class Pool
{
    /**
     * @var \SplQueue
     */
    protected $queue;

    /** @var \Closure */
    public $new;

    protected $idleTime = 60;

    public function __construct(\Closure $fn)
    {
        $this->queue = new \SplQueue();
        $this->new = $fn;
    }

    public function get(): \StdClass
    {
        $now = time();
        if (!$this->queue->isEmpty()) {
            $ele = $this->queue->dequeue();
            if (($now - $ele->t) > $this->idleTime && !$this->queue->isEmpty()) {
                return $this->queue->dequeue();
            }
            return $ele;
        }
        return $this->gen();
    }

    public function put(\StdClass $ele)
    {
        $now = time();
        if (($now - $ele->t) > $this->idleTime) {
            return;
        }
        $ele->t = $now;
        $this->queue->enqueue($ele);
    }

    protected function gen(): \StdClass
    {
        $ctx = ($this->new)();
        $c = new \StdClass();
        $c->val = $ctx;
        $c->t = time();
        return $c;
    }
}
