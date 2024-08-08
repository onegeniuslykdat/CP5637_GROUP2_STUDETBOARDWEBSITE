<?php

namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use Staatic\Vendor\AsyncAws\Core\AwsError\AwsError;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
trait HttpExceptionTrait
{
    private $response;
    private $awsError;
    public function __construct(ResponseInterface $response, ?AwsError $awsError = null)
    {
        $this->response = $response;
        $code = $response->getInfo('http_code');
        $url = $response->getInfo('url');
        $message = sprintf('HTTP %d returned for "%s".', $code, $url);
        if (null !== $this->awsError = $awsError) {
            $message .= <<<TEXT


Code:    {$this->awsError->getCode()}
Message: {$this->awsError->getMessage()}
Type:    {$this->awsError->getType()}
Detail:  {$this->awsError->getDetail()}

TEXT;
        }
        parent::__construct($message, $code);
        $this->populateResult($response);
    }
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
    public function getAwsCode(): ?string
    {
        return $this->awsError ? $this->awsError->getCode() : null;
    }
    public function getAwsType(): ?string
    {
        return $this->awsError ? $this->awsError->getType() : null;
    }
    public function getAwsMessage(): ?string
    {
        return $this->awsError ? $this->awsError->getMessage() : null;
    }
    public function getAwsDetail(): ?string
    {
        return $this->awsError ? $this->awsError->getDetail() : null;
    }
    /**
     * @param ResponseInterface $response
     */
    protected function populateResult($response): void
    {
    }
}
