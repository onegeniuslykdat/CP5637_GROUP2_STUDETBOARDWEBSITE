<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Exception;

use Throwable;
use Staatic\Vendor\Psr\Container\NotFoundExceptionInterface;
class ServiceNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string|null
     */
    private $sourceId;
    /**
     * @var mixed[]
     */
    private $alternatives;
    public function __construct(string $id, string $sourceId = null, Throwable $previous = null, array $alternatives = [], string $msg = null)
    {
        if (null !== $msg) {
        } elseif (null === $sourceId) {
            $msg = sprintf('You have requested a non-existent service "%s".', $id);
        } else {
            $msg = sprintf('The service "%s" has a dependency on a non-existent service "%s".', $sourceId, $id);
        }
        if ($alternatives) {
            if (1 == \count($alternatives)) {
                $msg .= ' Did you mean this: "';
            } else {
                $msg .= ' Did you mean one of these: "';
            }
            $msg .= implode('", "', $alternatives) . '"?';
        }
        parent::__construct($msg, 0, $previous);
        $this->id = $id;
        $this->sourceId = $sourceId;
        $this->alternatives = $alternatives;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getSourceId()
    {
        return $this->sourceId;
    }
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}
