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
    /** @var \Swoole\Http\Request */
    protected $request;

    public function __construct(\Swoole\Http\Request $request)
    {
        $this->request = $request;
    }

    public function getHeader(string $key): string
    {
        if (isset($this->request->header[$key])) {
            return $this->request->header[$key];
        }
        return "";
    }

    public function getQuery(string $key): string
    {
        if (isset($this->request->get[$key])) {
            return $this->request->get[$key];
        }
        return "";
    }

    public function getQuerys(): array
    {
        return $this->request->get;
    }

    public function postForm(string $key): string
    {
        if (isset($this->request->post[$key])) {
            return $this->request->post[$key];
        }
        return "";
    }

    public function postForms(): array
    {
        return $this->request->post;
    }

    public function getRawContent(): string
    {
        return $this->request->rawContent();
    }

    public function getMethod(): string
    {
        return $this->request->server["request_method"];
    }

    public function getUri(): string
    {
        return $this->request->server["request_uri"];
    }
}