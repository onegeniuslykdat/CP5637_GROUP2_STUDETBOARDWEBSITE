<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

class TypedReference extends Reference
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var mixed[]
     */
    private $attributes;
    public function __construct(string $id, string $type, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, string $name = null, array $attributes = [])
    {
        $this->name = ($type === $id) ? $name : null;
        parent::__construct($id, $invalidBehavior);
        $this->type = $type;
        $this->attributes = $attributes;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
