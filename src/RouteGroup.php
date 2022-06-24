<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 20:27
 */


namespace Zorro;


class RouteGroup
{
    protected $groupName;

    protected $routes = [];

    protected $routeGroups = [];

    public function Get(string $path, \Closure $handle): void
    {
        $this->routes["GET"][$path] = $handle;
    }

    public function Post(string $path, \Closure $handle): void
    {
        $this->routes["POST"][$path] = $handle;
    }

    public function Put(string $path, \Closure $handle): void
    {
        $this->routes["PUT"][$path] = $handle;
    }

    public function Delete(string $path, \Closure $handle): void
    {
        $this->routes["DELETE"][$path] = $handle;
    }

    public function Patch(string $path, \Closure $handle): void
    {
        $this->routes["PATCH"][$path] = $handle;
    }

    public function Head(string $path, \Closure $handle): void
    {
        $this->routes["HEAD"][$path] = $handle;
    }

    public function Group(string $group): RouteGroup
    {
        $routeGroup = new RouteGroup();
        $this->groupName = $group;
        $this->routeGroups[$group] = $routeGroup;
        return $routeGroup;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getGroups(): array
    {
        return $this->routeGroups;
    }
}