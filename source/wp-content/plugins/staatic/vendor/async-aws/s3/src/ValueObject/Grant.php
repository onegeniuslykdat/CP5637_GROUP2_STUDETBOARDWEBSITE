<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\Permission;
final class Grant
{
    private $grantee;
    private $permission;
    public function __construct(array $input)
    {
        $this->grantee = isset($input['Grantee']) ? Grantee::create($input['Grantee']) : null;
        $this->permission = $input['Permission'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getGrantee(): ?Grantee
    {
        return $this->grantee;
    }
    public function getPermission(): ?string
    {
        return $this->permission;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->grantee) {
            $node->appendChild($child = $document->createElement('Grantee'));
            $child->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $v->requestBody($child, $document);
        }
        if (null !== $v = $this->permission) {
            if (!Permission::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "Permission" for "%s". The value "%s" is not a valid "Permission".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('Permission', $v));
        }
    }
}
