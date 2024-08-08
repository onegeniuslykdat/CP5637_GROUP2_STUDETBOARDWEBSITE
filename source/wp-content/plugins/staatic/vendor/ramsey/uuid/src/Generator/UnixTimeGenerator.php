<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Brick\Math\BigInteger;
use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use function hash;
use function pack;
use function str_pad;
use function strlen;
use function substr;
use function substr_replace;
use function unpack;
use const PHP_INT_SIZE;
use const STR_PAD_LEFT;
class UnixTimeGenerator implements TimeGeneratorInterface
{
    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;
    /**
     * @var int
     */
    private $intSize = PHP_INT_SIZE;
    /**
     * @var string
     */
    private static $time = '';
    /**
     * @var string|null
     */
    private static $seed;
    /**
     * @var int
     */
    private static $seedIndex = 0;
    /**
     * @var mixed[]
     */
    private static $rand = [];
    /**
     * @var mixed[]
     */
    private static $seedParts;
    public function __construct(RandomGeneratorInterface $randomGenerator, int $intSize = PHP_INT_SIZE)
    {
        $this->randomGenerator = $randomGenerator;
        $this->intSize = $intSize;
    }
    /**
     * @param int|null $clockSeq
     * @param DateTimeInterface|null $dateTime
     */
    public function generate($node = null, $clockSeq = null, $dateTime = null): string
    {
        $time = ($dateTime ?? new DateTimeImmutable('now'))->format('Uv');
        if ($time > self::$time || $dateTime !== null && $time !== self::$time) {
            $this->randomize($time);
        } else {
            $time = $this->increment();
        }
        if ($this->intSize >= 8) {
            $time = substr(pack('J', (int) $time), -6);
        } else {
            $time = str_pad(BigInteger::of($time)->toBytes(\false), 6, "\x00", STR_PAD_LEFT);
        }
        return $time . pack('n*', self::$rand[1], self::$rand[2], self::$rand[3], self::$rand[4], self::$rand[5]);
    }
    private function randomize(string $time): void
    {
        if (self::$seed === null) {
            $seed = $this->randomGenerator->generate(16);
            self::$seed = $seed;
        } else {
            $seed = $this->randomGenerator->generate(10);
        }
        $rand = unpack('n*', $seed);
        $rand[1] &= 0x3ff;
        self::$rand = $rand;
        self::$time = $time;
    }
    private function increment(): string
    {
        if (self::$seedIndex === 0 && self::$seed !== null) {
            self::$seed = hash('sha512', self::$seed, \true);
            $s = unpack('l*', self::$seed);
            $s[] = $s[1] >> 8 & 0xff0000 | $s[2] >> 16 & 0xff00 | $s[3] >> 24 & 0xff;
            $s[] = $s[4] >> 8 & 0xff0000 | $s[5] >> 16 & 0xff00 | $s[6] >> 24 & 0xff;
            $s[] = $s[7] >> 8 & 0xff0000 | $s[8] >> 16 & 0xff00 | $s[9] >> 24 & 0xff;
            $s[] = $s[10] >> 8 & 0xff0000 | $s[11] >> 16 & 0xff00 | $s[12] >> 24 & 0xff;
            $s[] = $s[13] >> 8 & 0xff0000 | $s[14] >> 16 & 0xff00 | $s[15] >> 24 & 0xff;
            self::$seedParts = $s;
            self::$seedIndex = 21;
        }
        self::$rand[5] = 0xffff & $carry = self::$rand[5] + 1 + (self::$seedParts[self::$seedIndex--] & 0xffffff);
        self::$rand[4] = 0xffff & $carry = self::$rand[4] + ($carry >> 16);
        self::$rand[3] = 0xffff & $carry = self::$rand[3] + ($carry >> 16);
        self::$rand[2] = 0xffff & $carry = self::$rand[2] + ($carry >> 16);
        self::$rand[1] += $carry >> 16;
        if (0xfc00 & self::$rand[1]) {
            $time = self::$time;
            $mtime = (int) substr($time, -9);
            if ($this->intSize >= 8 || strlen($time) < 10) {
                $time = (string) ((int) $time + 1);
            } elseif ($mtime === 999999999) {
                $time = 1 + (int) substr($time, 0, -9) . '000000000';
            } else {
                $mtime++;
                $time = substr_replace($time, str_pad((string) $mtime, 9, '0', STR_PAD_LEFT), -9);
            }
            $this->randomize($time);
        }
        return self::$time;
    }
}
