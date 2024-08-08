<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Builder;

use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\BuilderNotFoundException;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
class FallbackBuilder implements UuidBuilderInterface
{
    /**
     * @var iterable
     */
    private $builders;
    public function __construct(iterable $builders)
    {
        $this->builders = $builders;
    }
    /**
     * @param CodecInterface $codec
     * @param string $bytes
     */
    public function build($codec, $bytes): UuidInterface
    {
        $lastBuilderException = null;
        foreach ($this->builders as $builder) {
            try {
                return $builder->build($codec, $bytes);
            } catch (UnableToBuildUuidException $exception) {
                $lastBuilderException = $exception;
                continue;
            }
        }
        throw new BuilderNotFoundException('Could not find a suitable builder for the provided codec and fields', 0, $lastBuilderException);
    }
}
