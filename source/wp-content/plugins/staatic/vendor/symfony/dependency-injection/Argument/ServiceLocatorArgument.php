<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

class ServiceLocatorArgument implements ArgumentInterface
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @var TaggedIteratorArgument|null
     */
    private $taggedIteratorArgument;
    /**
     * @param mixed[]|TaggedIteratorArgument $values
     */
    public function __construct($values = [])
    {
        if ($values instanceof TaggedIteratorArgument) {
            $this->taggedIteratorArgument = $values;
            $values = [];
        }
        $this->setValues($values);
    }
    public function getTaggedIteratorArgument(): ?TaggedIteratorArgument
    {
        return $this->taggedIteratorArgument;
    }
    public function getValues(): array
    {
        return $this->values;
    }
    /**
     * @param mixed[] $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }
}
