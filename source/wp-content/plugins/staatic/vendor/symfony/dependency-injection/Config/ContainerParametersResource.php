<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Config;

use Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface;
class ContainerParametersResource implements ResourceInterface
{
    /**
     * @var mixed[]
     */
    private $parameters;
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }
    public function __toString(): string
    {
        return 'container_parameters_' . md5(serialize($this->parameters));
    }
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
