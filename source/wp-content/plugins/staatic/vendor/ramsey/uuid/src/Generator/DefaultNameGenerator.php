<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Exception\NameException;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use ValueError;
use function hash;
class DefaultNameGenerator implements NameGeneratorInterface
{
    /**
     * @param UuidInterface $ns
     * @param string $name
     * @param string $hashAlgorithm
     */
    public function generate($ns, $name, $hashAlgorithm): string
    {
        try {
            $bytes = @hash($hashAlgorithm, $ns->getBytes() . $name, \true);
        } catch (ValueError $e) {
            $bytes = \false;
        }
        if ($bytes === \false) {
            throw new NameException(sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hashAlgorithm));
        }
        return (string) $bytes;
    }
}
