<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/21
 * Time: 23:16
 */


namespace Zorro\Aspect;


class PointContext
{
    /** @var int */
    private $index;

    /** @var \Closure[] */
    private $handles;

    private $bizHandleRes;

    public function __construct($handles)
    {
        $this->index = -1;
        $this->handles = $handles;
    }

    public function next(array $args)
    {
        $this->index++;
        while ($this->index < count($this->handles)) {
            //最后一个handle为业务函数，有返回值
            $handle = $this->handles[$this->index];
            if ($this->index + 1 === count($this->handles)) {
                $this->bizHandleRes = $handle(...$args);
            } else {
                $handle($this, $args);
            }
            $this->index++;
        }
        return $this->bizHandleRes;
    }
}
