<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:07
 */


namespace Zorro\Http;


trait Request
{
    /** @var \Swoole\Http\Request */
    protected $request;

    public function getHeader($key)
    {
        if (isset($this->request->header[$key])) {
            return $this->request->header[$key];
        }
        return null;
    }

    public function getParam(string $name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        return null;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getQuery($key)
    {
        if (isset($this->request->get[$key])) {
            return $this->request->get[$key];
        }
        return null;
    }

    public function getQuerys(): array
    {
        return $this->request->get;
    }

    public function postForm(string $key)
    {
        if (isset($this->request->post[$key])) {
            return $this->request->post[$key];
        }
        return null;
    }

    public function postForms(): array
    {
        return $this->request->post;
    }

    public function getRawContent()
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