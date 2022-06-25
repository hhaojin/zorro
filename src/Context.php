<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/23
 * Time: 21:15
 */

namespace Zorro;

use Zorro\Http\Header;
use Zorro\Http\Request;
use Zorro\Http\Response;
use Zorro\Serializer\Serializer;

class Context
{
    use Request, Response;

    const abortIndex = 999;

    protected $datas = [];

    /** @var Serializer */
    protected $serializer;

    protected $params = [];

    protected $handles = [];

    protected $index = -1;

    public function __construct($req, $resp)
    {
        $this->request = $req;
        $this->response = $resp;
    }

    public function setSerializer(Serializer $serializer): void
    {
        $this->serializer = $serializer;
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

    public function bindJson(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeJson) {
            return new $dest;
        }
        return $this->serializer->jsonUnmarshal($this->getRawContent(), $dest);
    }

    public function bindXml(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeXml) {
            return new $dest;
        }
        return $this->serializer->xmlUnmarshal($this->getRawContent(), $dest);
    }

    public function bindYaml(string $dest)
    {
        if ($this->getHeader(Header::ContentType) !== Header::ContentTypeYaml) {
            return new $dest;
        }
        return $this->serializer->yamlUnmarshal($this->getRawContent(), $dest);
    }

    public function bindQuery(string $dest)
    {
        return $this->serializer->getMapper()->Unmarsharl($this->getQuerys(), $dest);
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

}
