<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS)]
class AsTaggedItem
{
    /**
     * @var string|null
     */
    public $index;
    /**
     * @var int|null
     */
    public $priority;
    public function __construct(?string $index = null, ?int $priority = null)
    {
        $this->index = $index;
        $this->priority = $priority;
    }
}
