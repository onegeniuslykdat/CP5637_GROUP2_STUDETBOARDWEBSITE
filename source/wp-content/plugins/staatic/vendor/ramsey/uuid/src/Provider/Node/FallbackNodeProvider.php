<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Node;

use Staatic\Vendor\Ramsey\Uuid\Exception\NodeException;
use Staatic\Vendor\Ramsey\Uuid\Provider\NodeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
class FallbackNodeProvider implements NodeProviderInterface
{
    /**
     * @var iterable
     */
    private $providers;
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }
    public function getNode(): Hexadecimal
    {
        $lastProviderException = null;
        foreach ($this->providers as $provider) {
            try {
                return $provider->getNode();
            } catch (NodeException $exception) {
                $lastProviderException = $exception;
                continue;
            }
        }
        throw new NodeException('Unable to find a suitable node provider', 0, $lastProviderException);
    }
}
