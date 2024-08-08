<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\DateTimeException;
use Throwable;
use function str_pad;
use const STR_PAD_LEFT;
trait TimeTrait
{
    public function getDateTime(): DateTimeInterface
    {
        $time = $this->timeConverter->convertTime($this->fields->getTimestamp());
        try {
            return new DateTimeImmutable('@' . $time->getSeconds()->toString() . '.' . str_pad($time->getMicroseconds()->toString(), 6, '0', STR_PAD_LEFT));
        } catch (Throwable $e) {
            throw new DateTimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
