<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/27
 * Time: 21:58
 */


namespace Zorro\Aspect;


interface AspectInterface
{
    //example cache, log, traceing, metric
    public function handle(PointContext $ctx, array $args): void;
}
