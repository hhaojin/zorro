<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/21
 * Time: 22:33
 */


namespace Zorro;


class Proxy
{
    protected $originClass;

    protected $proxyMethods = [];

    public function __construct($name, $proxyMethods)
    {
        $this->proxyMethods[$name] = $proxyMethods;
    }

    public function __call($name, $arguments)
    {
        if (!array_key_exists($name, $this->proxyMethods)) {
            return (new $this->originClass)->{$name}($arguments);
        }
        $ctx = new InjectContext($this->proxyMethods[$name]);
        return $ctx->next($arguments);
    }
}
