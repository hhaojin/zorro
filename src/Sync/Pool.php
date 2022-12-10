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
    /** @var \SplQueue */
    protected $queue;

    /** @var \Closure */
    protected $new;

    /** @var int  */
    protected $idleTime = 60;

    /** @var int */
    protected $size;

    public function __construct(\Closure $fn, $size = 50, $idleTime = 60)
    {
        $this->queue = new \SplQueue();
        $this->new = $fn;
        $this->size = $size;
        $this->idleTime = $idleTime;
    }

    public function get(): PoolElement
    {
        $now = time();
        if (!$this->queue->isEmpty()) {
            /** @var PoolElement $ele */
            $ele = $this->queue->dequeue();
            if (($now - $ele->lastUseTime) > $this->idleTime && !$this->queue->isEmpty()) {
                return $this->queue->dequeue();
            }
            return $ele;
        }
        $ctx = ($this->new)();
        $n = new PoolElement($ctx);
        return $n;
    }

    public function put(PoolElement $ele)
    {
        $now = time();
        if (($now - $ele->lastUseTime) > $this->idleTime) {
            return;
        }
        if ($this->queue->count() >= $this->size) {
            return;
        }
        $ele->lastUseTime = $now;
        $this->queue->enqueue($ele);
    }

}
