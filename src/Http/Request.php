<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:07
 */


namespace Zorro\Http;


class Request implements RequsetInterface
{
    public $header = [];

    public $query = [];

    public $post = [];

    public $raw = "";

    public $method = "";

    public $uri = "";

    public function getHeader(string $key): string
    {
        if (isset($this->header[$key])) {
            return $this->header[$key];
        }
        return "";
    }

    public function getQuery(string $key): string
    {
        if (isset($this->query[$key])) {
            return $this->query[$key];
        }
        return "";
    }

    public function getQuerys(): array
    {
        return $this->query;
    }

    public function postForm(string $key): string
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        return "";
    }

    public function postForms(): array
    {
        return $this->post;
    }

    public function getRawContent(): string
    {
        return $this->raw;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}