<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Exception\NameException;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use function sprintf;
use function uuid_generate_md5;
use function uuid_generate_sha1;
use function uuid_parse;
class PeclUuidNameGenerator implements NameGeneratorInterface
{
    /**
     * @param UuidInterface $ns
     * @param string $name
     * @param string $hashAlgorithm
     */
    public function generate($ns, $name, $hashAlgorithm): string
    {
        switch ($hashAlgorithm) {
            case 'md5':
                $uuid = uuid_generate_md5($ns->toString(), $name);
                break;
            case 'sha1':
                $uuid = uuid_generate_sha1($ns->toString(), $name);
                break;
            default:
                throw new NameException(sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hashAlgorithm));
        }
        return uuid_parse($uuid);
    }
}
