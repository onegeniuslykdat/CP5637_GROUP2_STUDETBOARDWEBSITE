<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\ValueObject\ServerSideEncryptionByDefault;
use Staatic\Vendor\AsyncAws\S3\ValueObject\ServerSideEncryptionConfiguration;
use Staatic\Vendor\AsyncAws\S3\ValueObject\ServerSideEncryptionRule;
class GetBucketEncryptionOutput extends Result
{
    private $serverSideEncryptionConfiguration;
    public function getServerSideEncryptionConfiguration(): ?ServerSideEncryptionConfiguration
    {
        $this->initialize();
        return $this->serverSideEncryptionConfiguration;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $data = new SimpleXMLElement($response->getContent());
        $this->serverSideEncryptionConfiguration = new ServerSideEncryptionConfiguration(['Rules' => $this->populateResultServerSideEncryptionRules($data->Rule)]);
    }
    private function populateResultServerSideEncryptionRules(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new ServerSideEncryptionRule(['ApplyServerSideEncryptionByDefault' => (!$item->ApplyServerSideEncryptionByDefault) ? null : new ServerSideEncryptionByDefault(['SSEAlgorithm' => (string) $item->ApplyServerSideEncryptionByDefault->SSEAlgorithm, 'KMSMasterKeyID' => ($v = $item->ApplyServerSideEncryptionByDefault->KMSMasterKeyID) ? (string) $v : null]), 'BucketKeyEnabled' => ($v = $item->BucketKeyEnabled) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null]);
        }
        return $items;
    }
}
