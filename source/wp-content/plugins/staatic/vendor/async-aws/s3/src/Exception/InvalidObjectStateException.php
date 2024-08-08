<?php

namespace Staatic\Vendor\AsyncAws\S3\Exception;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\ClientException;
use Staatic\Vendor\AsyncAws\S3\Enum\IntelligentTieringAccessTier;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class InvalidObjectStateException extends ClientException
{
    private $storageClass;
    private $accessTier;
    public function getAccessTier(): ?string
    {
        return $this->accessTier;
    }
    public function getStorageClass(): ?string
    {
        return $this->storageClass;
    }
    /**
     * @param ResponseInterface $response
     */
    protected function populateResult($response): void
    {
        $data = new SimpleXMLElement($response->getContent(\false));
        if (0 < $data->Error->count()) {
            $data = $data->Error;
        }
        $this->storageClass = ($v = $data->StorageClass) ? (string) $v : null;
        $this->accessTier = ($v = $data->AccessTier) ? (string) $v : null;
    }
}
