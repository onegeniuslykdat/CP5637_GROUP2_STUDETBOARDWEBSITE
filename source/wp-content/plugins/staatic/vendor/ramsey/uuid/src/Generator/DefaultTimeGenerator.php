<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Exception\RandomSourceException;
use Staatic\Vendor\Ramsey\Uuid\Exception\TimeSourceException;
use Staatic\Vendor\Ramsey\Uuid\Provider\NodeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Throwable;
use function dechex;
use function hex2bin;
use function is_int;
use function pack;
use function preg_match;
use function sprintf;
use function str_pad;
use function strlen;
use const STR_PAD_LEFT;
class DefaultTimeGenerator implements TimeGeneratorInterface
{
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;
    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;
    /**
     * @var TimeProviderInterface
     */
    private $timeProvider;
    public function __construct(NodeProviderInterface $nodeProvider, TimeConverterInterface $timeConverter, TimeProviderInterface $timeProvider)
    {
        $this->nodeProvider = $nodeProvider;
        $this->timeConverter = $timeConverter;
        $this->timeProvider = $timeProvider;
    }
    /**
     * @param int|null $clockSeq
     */
    public function generate($node = null, $clockSeq = null): string
    {
        if ($node instanceof Hexadecimal) {
            $node = $node->toString();
        }
        $node = $this->getValidNode($node);
        if ($clockSeq === null) {
            try {
                $clockSeq = random_int(0, 0x3fff);
            } catch (Throwable $exception) {
                throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
            }
        }
        $time = $this->timeProvider->getTime();
        $uuidTime = $this->timeConverter->calculateTime($time->getSeconds()->toString(), $time->getMicroseconds()->toString());
        $timeHex = str_pad($uuidTime->toString(), 16, '0', STR_PAD_LEFT);
        if (strlen($timeHex) !== 16) {
            throw new TimeSourceException(sprintf('The generated time of \'%s\' is larger than expected', $timeHex));
        }
        $timeBytes = (string) hex2bin($timeHex);
        return $timeBytes[4] . $timeBytes[5] . $timeBytes[6] . $timeBytes[7] . $timeBytes[2] . $timeBytes[3] . $timeBytes[0] . $timeBytes[1] . pack('n*', $clockSeq) . $node;
    }
    /**
     * @param int|string|null $node
     */
    private function getValidNode($node): string
    {
        if ($node === null) {
            $node = $this->nodeProvider->getNode();
        }
        if (is_int($node)) {
            $node = dechex($node);
        }
        if (!preg_match('/^[A-Fa-f0-9]+$/', (string) $node) || strlen((string) $node) > 12) {
            throw new InvalidArgumentException('Invalid node value');
        }
        return (string) hex2bin(str_pad((string) $node, 12, '0', STR_PAD_LEFT));
    }
}
