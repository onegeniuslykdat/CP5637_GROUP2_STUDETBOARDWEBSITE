<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Grant;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Grantee;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Owner;
class GetObjectAclOutput extends Result
{
    private $owner;
    private $grants;
    private $requestCharged;
    public function getGrants(): array
    {
        $this->initialize();
        return $this->grants;
    }
    public function getOwner(): ?Owner
    {
        $this->initialize();
        return $this->owner;
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->owner = (!$data->Owner) ? null : new Owner(['DisplayName' => ($v = $data->Owner->DisplayName) ? (string) $v : null, 'ID' => ($v = $data->Owner->ID) ? (string) $v : null]);
        $this->grants = (!$data->AccessControlList) ? [] : $this->populateResultGrants($data->AccessControlList);
    }
    private function populateResultGrants(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml->Grant as $item) {
            $items[] = new Grant(['Grantee' => (!$item->Grantee) ? null : new Grantee(['DisplayName' => ($v = $item->Grantee->DisplayName) ? (string) $v : null, 'EmailAddress' => ($v = $item->Grantee->EmailAddress) ? (string) $v : null, 'ID' => ($v = $item->Grantee->ID) ? (string) $v : null, 'Type' => (string) ($item->Grantee->attributes('xsi', \true)['type'][0] ?? null), 'URI' => ($v = $item->Grantee->URI) ? (string) $v : null]), 'Permission' => ($v = $item->Permission) ? (string) $v : null]);
        }
        return $items;
    }
}
