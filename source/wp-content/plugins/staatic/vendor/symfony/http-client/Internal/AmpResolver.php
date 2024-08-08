<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

use Staatic\Vendor\Amp\Dns\Resolver;
use Staatic\Vendor\Amp\Dns;
use Staatic\Vendor\Amp\Dns\Record;
use Staatic\Vendor\Amp\Promise;
use Staatic\Vendor\Amp\Success;
class AmpResolver implements Resolver
{
    /**
     * @var mixed[]
     */
    private $dnsMap;
    public function __construct(array &$dnsMap)
    {
        $this->dnsMap =& $dnsMap;
    }
    /**
     * @param string $name
     * @param int|null $typeRestriction
     */
    public function resolve($name, $typeRestriction = null): Promise
    {
        if (!isset($this->dnsMap[$name]) || !\in_array($typeRestriction, [Record::A, null], \true)) {
            return Dns\resolver()->resolve($name, $typeRestriction);
        }
        return new Success([new Record($this->dnsMap[$name], Record::A, null)]);
    }
    /**
     * @param string $name
     * @param int $type
     */
    public function query($name, $type): Promise
    {
        if (!isset($this->dnsMap[$name]) || Record::A !== $type) {
            return Dns\resolver()->query($name, $type);
        }
        return new Success([new Record($this->dnsMap[$name], Record::A, null)]);
    }
}
