<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/12/11
 * Time: 0:22
 */


namespace Zorro\Http\Server\Adapter;

use Zorro\Http\Server\Adapter\Swoole\SwooleAdapter;
use Zorro\Http\Server\Adapter\Workerman\WorkerManAdapter;
use Zorro\Http\Server\AdapterAbstract;
use Zorro\Zorro;

class Adapter
{
    const Server = "--SERVER";
    const Workerman = "workerman";
    const Swoole = "swoole";

    public static function Serve(Zorro $zorro): AdapterAbstract
    {
        $opt = getopt("SERVER", ["SERVER:"]);
        $type = $opt["SERVER"] ?? self::Workerman;
        switch ($type) {
            case self::Workerman:
                $server = new WorkerManAdapter($zorro);
                return $server;
            case self::Swoole:
                $server = new SwooleAdapter($zorro);
                return $server;
            default:
                throw new \Exception("nonsupport server adapter");
        }
    }

}
