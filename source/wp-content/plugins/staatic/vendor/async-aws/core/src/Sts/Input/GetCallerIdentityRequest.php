<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Input;

use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
final class GetCallerIdentityRequest extends Input
{
    public function __construct(array $input = [])
    {
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/x-www-form-urlencoded'];
        $query = [];
        $uriString = '/';
        $body = http_build_query(['Action' => 'GetCallerIdentity', 'Version' => '2011-06-15'] + $this->requestBody(), '', '&', \PHP_QUERY_RFC1738);
        return new Request('POST', $uriString, $query, $headers, StreamFactory::create($body));
    }
    private function requestBody(): array
    {
        $payload = [];
        return $payload;
    }
}
