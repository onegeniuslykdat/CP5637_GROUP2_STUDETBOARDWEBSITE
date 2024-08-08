<?php

namespace Staatic\Vendor\AsyncAws\CloudFront\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\CloudFront\ValueObject\InvalidationBatch;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
final class CreateInvalidationRequest extends Input
{
    private $distributionId;
    private $invalidationBatch;
    public function __construct(array $input = [])
    {
        $this->distributionId = $input['DistributionId'] ?? null;
        $this->invalidationBatch = isset($input['InvalidationBatch']) ? InvalidationBatch::create($input['InvalidationBatch']) : null;
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getDistributionId(): ?string
    {
        return $this->distributionId;
    }
    public function getInvalidationBatch(): ?InvalidationBatch
    {
        return $this->invalidationBatch;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        $query = [];
        $uri = [];
        if (null === $v = $this->distributionId) {
            throw new InvalidArgument(sprintf('Missing parameter "DistributionId" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['DistributionId'] = $v;
        $uriString = '/2019-03-26/distribution/' . rawurlencode($uri['DistributionId']) . '/invalidation';
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';
        return new Request('POST', $uriString, $query, $headers, StreamFactory::create($body));
    }
    /**
     * @param string|null $value
     */
    public function setDistributionId($value): self
    {
        $this->distributionId = $value;
        return $this;
    }
    /**
     * @param InvalidationBatch|null $value
     */
    public function setInvalidationBatch($value): self
    {
        $this->invalidationBatch = $value;
        return $this;
    }
    private function requestBody(DOMNode $node, DOMDocument $document): void
    {
        if (null === $v = $this->invalidationBatch) {
            throw new InvalidArgument(sprintf('Missing parameter "InvalidationBatch" for "%s". The value cannot be null.', __CLASS__));
        }
        $node->appendChild($child = $document->createElement('InvalidationBatch'));
        $child->setAttribute('xmlns', 'http://cloudfront.amazonaws.com/doc/2019-03-26/');
        $v->requestBody($child, $document);
    }
}
