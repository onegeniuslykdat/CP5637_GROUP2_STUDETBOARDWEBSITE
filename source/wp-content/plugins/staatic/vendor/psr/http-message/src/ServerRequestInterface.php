<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface ServerRequestInterface extends RequestInterface
{
    public function getServerParams();
    public function getCookieParams();
    /**
     * @param mixed[] $cookies
     */
    public function withCookieParams($cookies);
    public function getQueryParams();
    /**
     * @param mixed[] $query
     */
    public function withQueryParams($query);
    public function getUploadedFiles();
    /**
     * @param mixed[] $uploadedFiles
     */
    public function withUploadedFiles($uploadedFiles);
    public function getParsedBody();
    public function withParsedBody($data);
    public function getAttributes();
    /**
     * @param string $name
     */
    public function getAttribute($name, $default = null);
    /**
     * @param string $name
     */
    public function withAttribute($name, $value);
    /**
     * @param string $name
     */
    public function withoutAttribute($name);
}
