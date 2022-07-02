<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:27
 */


namespace Zorro;


use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class RouteGroup
{
    protected $groupName;

    protected $routes = [];

    protected $routeGroups = [];

    protected $handles = [];

    protected function handles(): array
    {
        $collector = new RouteCollector(new Std(), new GroupCountBased());
        $this->collectRouteGroup($collector, ["" => $this]);
        $this->handles = null;
        $this->routeGroups = null;
        $this->routes = null;
        return $collector->getData();
    }

    protected function collectRouteGroup(RouteCollector $collector, array $groups): void
    {
        /**
         * @var string $groupName
         * @var RouteGroup $routeGroup
         */
        foreach ($groups as $groupName => $routeGroup) {
            $collector->addGroup($groupName, function (RouteCollector $r) use ($collector, $routeGroup) {
                foreach ($routeGroup->getRoutes() as $method => $routes) {
                    foreach ($routes as $path => $handle) {
                        $handles = $routeGroup->getHandles();
                        if (is_callable($handle)) {
                            $handles[] = $handle;
                        }
                        if (is_array($handle) && count($handle) == 2 && BeanFactory::hasBean($handle[0])) {
                            $handles[] = function (Context $context) use ($handle) {
                                $bean = BeanFactory::getBean($handle[0]);
                                call_user_func([$bean, $handle[1]], $context);
                            };
                        }
                        $r->addRoute($method, $path, $handles);
                    }
                }
                $this->collectRouteGroup($collector, $routeGroup->getGroups());
            });
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getGroups(): array
    {
        return $this->routeGroups;
    }

    public function getHandles(): array
    {
        return $this->handles;
    }

    public function Group(string $group, HandleInterface ...$handles): RouteGroup
    {
        $routeGroup = new RouteGroup();
        array_push($routeGroup->handles, ...$this->parseHandle(...$handles));
        array_unshift($routeGroup->handles, ...$this->handles);
        $this->groupName = $group;
        $this->routeGroups[$group] = $routeGroup;
        return $routeGroup;
    }

    public function parseHandle(HandleInterface ...$handles): array
    {
        $result = [];
        foreach ($handles as $handle) {
            $rf = new \ReflectionClass($handle);
            $fn = $rf->getMethod("handle");
            $result[] = $fn->getClosure($handle);
        }
        return $result;
    }

    public function Use(HandleInterface ...$handles)
    {
        array_push($this->handles, ...$this->parseHandle(...$handles));
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Get(string $path, $handle): void
    {
        $this->routes["GET"][$path] = $handle;
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Post(string $path, $handle): void
    {
        $this->routes["POST"][$path] = $handle;
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Put(string $path, $handle): void
    {
        $this->routes["PUT"][$path] = $handle;
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Delete(string $path, $handle): void
    {
        $this->routes["DELETE"][$path] = $handle;
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Patch(string $path, $handle): void
    {
        $this->routes["PATCH"][$path] = $handle;
    }

    /**
     * @param string $path
     * @param callable|array $handle
     * @return void
     */
    public function Head(string $path, $handle): void
    {
        $this->routes["HEAD"][$path] = $handle;
    }

}
