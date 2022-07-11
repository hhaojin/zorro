<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/11
 * Time: 19:42
 */


namespace Zorro\Sync;


use Swoole\Timer;

class Pool
{
    /** @var ListNode */
    protected $head;

    /** @var ListNode */
    protected $tail;

    protected $size = 0;

    /** @var \Closure */
    public $new;

    protected $idleTime = 60;

    public function __construct()
    {
        $this->head = new ListNode();
        Timer::tick(60 * 1000, [$this, "relase"], $this->head);
    }

    private function relase(ListNode $head)
    {
        $t = time();
        while (($n = $head->next) !== null) {
            if ($t - $n->time < $this->idleTime) {
                break;
            }
            $this->head->next();
        }
    }

    public function get()
    {
        if ($this->size > 0) {
            $node = $this->head->next;
            $this->head->next();
            $this->size--;
            return $node;
        }
        $node = new ListNode();
        $node->val = ($this->new)();
        return $node;
    }

    public function put(ListNode $node)
    {
        $node->time = time();
        if ($this->size > 0) {
            $this->tail->push($node);
            $this->tail = $this->tail->next;
        } else {
            $this->head->push($node);
            $this->tail = $node;
        }
        $this->size++;
    }
}
