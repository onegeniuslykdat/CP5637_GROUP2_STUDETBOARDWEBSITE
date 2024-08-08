<?php

namespace Staatic\Vendor\AsyncAws\CloudFront\Result;

use SimpleXMLElement;
use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\CloudFront\ValueObject\Invalidation;
use Staatic\Vendor\AsyncAws\CloudFront\ValueObject\InvalidationBatch;
use Staatic\Vendor\AsyncAws\CloudFront\ValueObject\Paths;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class CreateInvalidationResult extends Result
{
    private $location;
    private $invalidation;
    public function getInvalidation(): ?Invalidation
    {
        $this->initialize();
        return $this->invalidation;
    }
    public function getLocation(): ?string
    {
        $this->initialize();
        return $this->location;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->location = $headers['location'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->invalidation = new Invalidation(['Id' => (string) $data->Id, 'Status' => (string) $data->Status, 'CreateTime' => new DateTimeImmutable((string) $data->CreateTime), 'InvalidationBatch' => new InvalidationBatch(['Paths' => new Paths(['Quantity' => (int) (string) $data->InvalidationBatch->Paths->Quantity, 'Items' => (!$data->InvalidationBatch->Paths->Items) ? null : $this->populateResultPathList($data->InvalidationBatch->Paths->Items)]), 'CallerReference' => (string) $data->InvalidationBatch->CallerReference])]);
    }
    private function populateResultPathList(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml->Path as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }
        return $items;
    }
}
