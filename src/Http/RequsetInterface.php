<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2022/6/27
 * Time: 19:27
 */


namespace Zorro\Http;


interface RequsetInterface
{
    public function getHeader(string $key): string;

    public function getQuery(string $key): string;

    public function getQuerys(): array;

    public function postForm(string $key): string;

    public function postForms(): array;

    public function getRawContent(): string;

    public function getMethod(): string;

    public function getUri(): string;
}