<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
final class ListBucketsRequest extends Input
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
        $headers = ['content-type' => 'application/xml'];
        $query = [];
        $uriString = '/';
        $body = '';
        return new Request('GET', $uriString, $query, $headers, StreamFactory::create($body));
    }
}
