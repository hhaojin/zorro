<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/7
 * Time: 20:48
 */


namespace Zorro\Http\Server;

use Zorro\Zorro;

abstract class AdapterAbstract
{
    /** @var Zorro */
    protected $zorro;

    public function __construct(Zorro $zorro)
    {
        $this->zorro = $zorro;
    }


    abstract function start(int $port = 8081, string $host = "0.0.0.0");
}