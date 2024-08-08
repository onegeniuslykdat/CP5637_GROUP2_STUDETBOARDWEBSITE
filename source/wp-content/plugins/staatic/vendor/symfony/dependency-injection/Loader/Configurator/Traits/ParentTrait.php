<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait ParentTrait
{
    /**
     * @param string $parent
     * @return static
     */
    final public function parent($parent)
    {
        if (!$this->allowParent) {
            throw new InvalidArgumentException(sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
        }
        if ($this->definition instanceof ChildDefinition) {
            $this->definition->setParent($parent);
        } else {
            $definition = serialize($this->definition);
            $definition = substr_replace($definition, '68', 2, 2);
            $definition = substr_replace($definition, 'Child', 59, 0);
            $definition = unserialize($definition);
            $this->definition = $definition->setParent($parent);
        }
        return $this;
    }
}
