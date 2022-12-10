<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/11
 * Time: 0:54
 */


namespace Zorro\Sync;

class PoolElement
{
    public $val;

    /** @var int  */
    public $lastUseTime;

    public function __construct($val)
    {
        $this->val = $val;
        $this->lastUseTime = time();
    }
}
