<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

trigger_deprecation('symfony/dependency-injection', '6.1', '"%s" is deprecated.', ReferenceSetArgumentTrait::class);
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
trait ReferenceSetArgumentTrait
{
    /**
     * @var mixed[]
     */
    private $values;
    public function __construct(array $values)
    {
        $this->setValues($values);
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
        foreach ($values as $k => $v) {
            if (null !== $v && !$v instanceof Reference) {
                throw new InvalidArgumentException(sprintf('A "%s" must hold only Reference instances, "%s" given.', __CLASS__, get_debug_type($v)));
            }
        }
        $this->values = $values;
    }
}
