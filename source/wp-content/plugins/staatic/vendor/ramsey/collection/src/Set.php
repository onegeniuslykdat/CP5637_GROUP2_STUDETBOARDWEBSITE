<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

class Set extends AbstractSet
{
    /**
     * @readonly
     * @var string
     */
    private $setType;
    public function __construct(string $setType, array $data = [])
    {
        $this->setType = $setType;
        parent::__construct($data);
    }
    public function getType(): string
    {
        return $this->setType;
    }
}
