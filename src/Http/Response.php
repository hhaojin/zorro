<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:10
 */


namespace Zorro\Http;


trait Response
{
    /** @var \Swoole\Http\Response */
    protected $response;

    protected $statusCode;

    protected $responseHeader;

    protected $responseBody;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getResponseHeader()
    {
        return $this->responseHeader;
    }

    public function getResponseBody()
    {
        return $this->responseBody;
    }

    public function setResponseBody($responseBody): void
    {
        $this->responseBody = $responseBody;
    }

    public function header($key, $value)
    {
        $this->responseHeader[$key] = $value;
    }

    public function status(int $code)
    {
        $this->statusCode = $code;
    }

    public function json(int $code, $body)
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeJson);;
        $this->setResponseBody($this->serializer->jsonMarshal($body));
    }

    public function xml(int $code, $body)
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeXml);
        $this->setResponseBody($this->serializer->xmlMarshal($body));
    }

    public function yaml(int $code, $body)
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeYaml);
        $this->setResponseBody($this->serializer->xmlMarshal($body));
    }

    public function end(): void
    {
        $this->response->status($this->getStatusCode());
        foreach ($this->getResponseHeader() as $key => $value) {
            $this->response->header($key, $value);
        }
        $body = $this->getResponseBody();
        if ($body !== null) {
            $this->response->end($body);
            return;
        }
        $this->response->end();
    }

}