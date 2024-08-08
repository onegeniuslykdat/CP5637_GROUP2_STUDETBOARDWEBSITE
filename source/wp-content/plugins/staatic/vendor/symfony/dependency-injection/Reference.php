<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

class Reference
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var int
     */
    private $invalidBehavior;
    public function __construct(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $this->id = $id;
        $this->invalidBehavior = $invalidBehavior;
    }
    public function __toString(): string
    {
        return $this->id;
    }
    public function getInvalidBehavior(): int
    {
        return $this->invalidBehavior;
    }
}
