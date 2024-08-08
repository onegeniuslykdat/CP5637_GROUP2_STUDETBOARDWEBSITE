<?php

namespace Staatic\Framework\CrawlResultHandler;

use Staatic\Crawler\ResponseUtil;
use Staatic\Framework\CrawlResult;
use Staatic\Framework\Resource;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Transformer\TransformerCollection;
final class CrawlResultHandler implements CrawlResultHandlerInterface
{
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var TransformerCollection
     */
    private $transformers;
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, TransformerCollection $transformers)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->transformers = $transformers;
    }
    /**
     * @param string $buildId
     * @param CrawlResult $crawlResult
     */
    public function handle($buildId, $crawlResult): void
    {
        if (!$crawlResult->response()) {
            return;
        }
        $resource = Resource::create($crawlResult->response()->getBody());
        $result = $this->createResult($buildId, $this->resultRepository->nextId(), $crawlResult, $resource);
        $this->transformers->apply($result, $resource);
        $this->resourceRepository->write($resource);
        $this->resultRepository->add($result);
    }
    private function createResult(string $buildId, string $resultId, CrawlResult $crawlResult, Resource $resource): Result
    {
        $response = $crawlResult->response();
        $mimeType = null;
        $charset = null;
        if ($response->hasHeader('Content-Type')) {
            [$mimeType, $charset] = ResponseUtil::parseContentTypeHeader($response->getHeaderLine('Content-Type'));
        }
        $redirectUrl = null;
        if (ResponseUtil::isRedirectResponse($response)) {
            $redirectUrl = ResponseUtil::getRedirectUrl($response);
        }
        return new Result($resultId, $buildId, $crawlResult->transformedUrl(), md5((string) $crawlResult->normalizedUrl()), $response->getStatusCode(), $resource->md5(), $resource->sha1(), $resource->size(), $mimeType, $charset, $redirectUrl, $crawlResult->url(), $crawlResult->foundOnUrl());
    }
}
