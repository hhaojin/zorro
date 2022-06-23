<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:03
 */

namespace Zorro;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use phpDocumentor\Reflection\Types\This;

class Zorro
{
    /** @var RouteCollector */
    protected $collector;

    /** @var array */
    protected $groups = [];

    /** @var Dispatcher */
    protected $dispatcher;

    public function __construct()
    {
        $this->collector = new RouteCollector(new Std(), new GroupCountBased());
    }

    public function Group(string $group): RouteGroup
    {
        $routeGroup = new RouteGroup($group);
        $this->groups[$group] = $routeGroup;
        return $routeGroup;
    }

    public function Get(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("GET", $path, $handle);
    }

    public function Post(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("POST", $path, $handle);
    }

    public function Put(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("PUT", $path, $handle);
    }

    public function Delete(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("DELETE", $path, $handle);
    }

    public function Patch(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("PATCH", $path, $handle);
    }

    public function Head(string $path, \Closure $handle): void
    {
        $this->collector->addRoute("HEAD", $path, $handle);
    }

    public function Run(): void
    {
        //run server
    }

    protected function initDispatcher()
    {
        $this->collectRouteGroup($this->collector, $this->groups);
        $this->dispatcher = new Dispatcher($this->collector->getData());
        $this->collector = null;
        $this->groups = null;
    }

    protected function collectRouteGroup(RouteCollector $collector, array $groups): void
    {
        /** @var RouteGroup $routeGroup */
        foreach ($groups as $group => $routeGroup) {
            $collector->addGroup($group, function (RouteCollector $r) use ($routeGroup) {
                foreach ($routeGroup->getRoutes() as $method => $routes) {
                    foreach ($routes as $path => $handle) {
                        $r->addRoute($method, $path, $handle);
                    }
                }
                $this->collectRouteGroup($r, $routeGroup->getGroups());
            });
        }
    }

    protected function dispatch($method, $uri): array
    {
        return $this->dispatcher->dispatch($method, $uri);
    }
}