<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

class Collection extends AbstractCollection
{
    /**
     * @readonly
     * @var string
     */
    private $collectionType;
    public function __construct(string $collectionType, array $data = [])
    {
        $this->collectionType = $collectionType;
        parent::__construct($data);
    }
    public function getType(): string
    {
        return $this->collectionType;
    }
}
