<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use Staatic\Vendor\AsyncAws\Core\Exception\Exception;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
interface HttpException extends Exception
{
    public function getResponse(): ResponseInterface;
    public function getAwsCode(): ?string;
    public function getAwsType(): ?string;
    public function getAwsMessage(): ?string;
    public function getAwsDetail(): ?string;
}
