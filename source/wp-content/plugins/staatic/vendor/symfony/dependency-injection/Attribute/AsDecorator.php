<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
#[Attribute(Attribute::TARGET_CLASS)]
class AsDecorator
{
    /**
     * @var string
     */
    public $decorates;
    /**
     * @var int
     */
    public $priority = 0;
    /**
     * @var int
     */
    public $onInvalid = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
    public function __construct(string $decorates, int $priority = 0, int $onInvalid = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $this->decorates = $decorates;
        $this->priority = $priority;
        $this->onInvalid = $onInvalid;
    }
}
