<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/26
 * Time: 16:22
 */


namespace Zorro\Http;


class Header
{
    const ContentType = "Content-Type";

    const ContentTypeText = "text/plain";
    const ContentTypeHtml = "text/html";
    const ContentTypeJson = "application/json";
    const ContentTypeXml = "application/xml";
    const ContentTypeYaml = "application/x-yaml";

    const Utf8Charset = "charset=utf-8";
}