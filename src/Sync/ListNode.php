<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/7/11
 * Time: 19:48
 */


namespace Zorro\Sync;

class ListNode
{
    /** @var ListNode */
    public $prev;

    /** @var ListNode */
    public $next;

    public $val;

    public $time;

    public function push(ListNode $node)
    {
        $node->prev = $this;
        $this->next = $node;
    }

    public function next()
    {
        $this->next = $this->next->next;
    }
}