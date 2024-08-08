<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Tag;
class GetObjectTaggingOutput extends Result
{
    private $versionId;
    private $tagSet;
    public function getTagSet(): array
    {
        $this->initialize();
        return $this->tagSet;
    }
    public function getVersionId(): ?string
    {
        $this->initialize();
        return $this->versionId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->tagSet = $this->populateResultTagSet($data->TagSet);
    }
    private function populateResultTagSet(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml->Tag as $item) {
            $items[] = new Tag(['Key' => (string) $item->Key, 'Value' => (string) $item->Value]);
        }
        return $items;
    }
}
