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

    protected $handles = [];

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

}
