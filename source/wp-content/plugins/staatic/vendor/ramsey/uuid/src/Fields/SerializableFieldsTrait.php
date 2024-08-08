<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Fields;

use ValueError;
use function base64_decode;
use function sprintf;
use function strlen;
trait SerializableFieldsTrait
{
    abstract public function __construct(string $bytes);
    abstract public function getBytes(): string;
    public function serialize(): string
    {
        return $this->getBytes();
    }
    public function __serialize(): array
    {
        return ['bytes' => $this->getBytes()];
    }
    /**
     * @param string $data
     */
    public function unserialize($data): void
    {
        if (strlen($data) === 16) {
            $this->__construct($data);
        } else {
            $this->__construct(base64_decode($data));
        }
    }
    public function __unserialize(array $data): void
    {
        if (!isset($data['bytes'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        $this->unserialize($data['bytes']);
    }
}
