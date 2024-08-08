<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
trait HttpExceptionTrait
{
    /**
     * @var ResponseInterface
     */
    private $response;
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $code = $response->getInfo('http_code');
        $url = $response->getInfo('url');
        $message = sprintf('HTTP %d returned for "%s".', $code, $url);
        $httpCodeFound = \false;
        $isJson = \false;
        foreach (array_reverse($response->getInfo('response_headers')) as $h) {
            if (strncmp($h, 'HTTP/', strlen('HTTP/')) === 0) {
                if ($httpCodeFound) {
                    break;
                }
                $message = sprintf('%s returned for "%s".', $h, $url);
                $httpCodeFound = \true;
            }
            if (0 === stripos($h, 'content-type:')) {
                if (preg_match('/\bjson\b/i', $h)) {
                    $isJson = \true;
                }
                if ($httpCodeFound) {
                    break;
                }
            }
        }
        if ($isJson && $body = json_decode($response->getContent(\false), \true)) {
            if (isset($body['hydra:title']) || isset($body['hydra:description'])) {
                $separator = isset($body['hydra:title'], $body['hydra:description']) ? "\n\n" : '';
                $message = ($body['hydra:title'] ?? '') . $separator . ($body['hydra:description'] ?? '');
            } elseif ((isset($body['title']) || isset($body['detail'])) && (\is_scalar($body['title'] ?? '') && \is_scalar($body['detail'] ?? ''))) {
                $separator = isset($body['title'], $body['detail']) ? "\n\n" : '';
                $message = ($body['title'] ?? '') . $separator . ($body['detail'] ?? '');
            }
        }
        parent::__construct($message, $code);
    }
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
