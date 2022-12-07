<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 21:15
 */

namespace Zorro;

use Zorro\Http\Header;
use Zorro\Http\RequsetInterface;
use Zorro\Http\ResponseInterface;
use Zorro\Serialize\Json;
use Zorro\Serialize\Parser\JsonParser;
use Zorro\Serialize\Xml;
use Zorro\Serialize\Yaml;

class Context
{
    /** @var RequsetInterface */
    public $request;

    /** @var ResponseInterface */
    public $response;

    const abortIndex = 999;

    protected $datas = [];

    protected $params = [];

    protected $handles = [];

    protected $index = -1;

    public function __construct(RequsetInterface $req = null, ResponseInterface $resp = null)
    {
        $this->request = $req;
        $this->response = $resp;
    }

    public function setHandles(array $handles): void
    {
        $this->handles = $handles;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function get($key)
    {
        if (isset($this->datas[$key])) {
            return $this->datas[$key];
        }
        return null;
    }

    public function set($key, $value)
    {
        $this->datas[$key] = $value;
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

    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    public function getUri(): string
    {
        return $this->request->getUri();
    }

    public function getHeader(string $key): string
    {
        return $this->request->getHeader($key);
    }

    public function getRawContent(): string
    {
        return $this->request->getRawContent();
    }

    public function getQuerys(): array
    {
        return $this->request->getQuerys();
    }

    public function end(): void
    {
        $this->response->end();
    }

    public function status(int $code): void
    {
        $this->response->status($code);
    }

    public function bindJson(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeJson) {
            return new $dest;
        }
        return Json::Unmarshal($this->getRawContent(), $dest);
    }

    public function bindXml(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeXml) {
            return new $dest;
        }
        return Xml::Unmarshal($this->getRawContent(), $dest);
    }

    public function bindYaml(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeYaml) {
            return new $dest;
        }
        return Yaml::Unmarshal($this->getRawContent(), $dest);
    }

    public function bindQuery(string $dest)
    {
        return Json::mapping($this->getQuerys(), $dest);
    }

    public function next(): void
    {
        $this->index++;
        while ($this->index < count($this->handles)) {
            $this->handles[$this->index]($this);
            $this->index++;
        }
    }

    public function abort(): void
    {
        $this->index = self::abortIndex;
    }

    public function isAborted(): bool
    {
        return $this->index === self::abortIndex;
    }

    public function abortJson(int $code, $body)
    {
        $this->abort();
        $this->json($code, $body);
    }

    public function abortXml(int $code, $body)
    {
        $this->abort();
        $this->xml($code, $body);
    }

    public function abortYaml(int $code, $body)
    {
        $this->abort();
        $this->yaml($code, $body);
    }

    public function abortStatus(int $code)
    {
        $this->abort();
        $this->status($code);
    }

    public function header(string $key, string $value): void
    {
        $this->response->header($key, $value);
    }

    public function string(int $code, string $body): void
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeText);
        $this->response->write($body);
    }

    public function html(int $code, string $body): void
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeHtml);
        $this->response->write($body);
    }

    public function json(int $code, $body): void
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeJson);
        $data = Json::Marshal($body, [JsonParser::EncodeOptions => JSON_UNESCAPED_UNICODE]);
        $this->response->write($data);
    }

    public function xml(int $code, $body): void
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeXml);
        $this->response->write(Xml::Marshal($body));
    }

    public function yaml(int $code, $body): void
    {
        $this->status($code);
        $this->header(Header::ContentType, Header::ContentTypeYaml);
        $this->response->write(Yaml::Marshal($body));
    }

    public function reset()
    {
        $this->params = null;
        $this->datas = null;
        $this->handles = null;
        $this->index = -1;
    }
}
