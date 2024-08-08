<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

class TypedMap extends AbstractTypedMap
{
    /**
     * @readonly
     * @var string
     */
    private $keyType;
    /**
     * @readonly
     * @var string
     */
    private $valueType;
    public function __construct(string $keyType, string $valueType, array $data = [])
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
        parent::__construct($data);
    }
    public function getKeyType(): string
    {
        return $this->keyType;
    }
    public function getValueType(): string
    {
        return $this->valueType;
    }
}
