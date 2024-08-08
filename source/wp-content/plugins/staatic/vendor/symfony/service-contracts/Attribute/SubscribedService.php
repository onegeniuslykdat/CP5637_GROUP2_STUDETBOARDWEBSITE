<?php

namespace Staatic\Vendor\Symfony\Contracts\Service\Attribute;

use Attribute;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceMethodsSubscriberTrait;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceSubscriberInterface;
#[Attribute(Attribute::TARGET_METHOD)]
final class SubscribedService
{
    /**
     * @var string|null
     */
    public $key;
    /**
     * @var string|null
     */
    public $type;
    /**
     * @var bool
     */
    public $nullable = \false;
    /**
     * @var mixed[]
     */
    public $attributes;
    /**
     * @param mixed[]|object $attributes
     */
    public function __construct(?string $key = null, ?string $type = null, bool $nullable = \false, $attributes = [])
    {
        $this->key = $key;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->attributes = \is_array($attributes) ? $attributes : [$attributes];
    }
}
