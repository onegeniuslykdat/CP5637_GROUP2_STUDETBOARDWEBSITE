<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use LengthException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Exception\InsufficientSetupException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\PrimeField;
class Hash
{
    const PADDING_KECCAK = 1;
    const PADDING_SHA3 = 2;
    const PADDING_SHAKE = 3;
    private $paddingType = 0;
    private $hashParam;
    private $length;
    private $algo;
    private $key = \false;
    private $nonce = \false;
    private $parameters = [];
    private $computedKey = \false;
    private $opad;
    private $ipad;
    private $recomputeAESKey;
    private $c;
    private $pad;
    private $blockSize;
    private static $factory36;
    private static $factory64;
    private static $factory128;
    private static $offset64;
    private static $offset128;
    private static $marker64;
    private static $marker128;
    private static $maxwordrange64;
    private static $maxwordrange128;
    public function __construct($hash = 'sha256')
    {
        $this->setHash($hash);
    }
    public function setKey($key = \false)
    {
        $this->key = $key;
        $this->computeKey();
        $this->recomputeAESKey = \true;
    }
    public function setNonce($nonce = \false)
    {
        switch (\true) {
            case !is_string($nonce):
            case strlen($nonce) > 0 && strlen($nonce) <= 16:
                $this->recomputeAESKey = \true;
                $this->nonce = $nonce;
                return;
        }
        throw new LengthException('The nonce length must be between 1 and 16 bytes, inclusive');
    }
    private function computeKey()
    {
        if ($this->key === \false) {
            $this->computedKey = \false;
            return;
        }
        if (strlen($this->key) <= $this->getBlockLengthInBytes()) {
            $this->computedKey = $this->key;
            return;
        }
        $this->computedKey = is_array($this->algo) ? call_user_func($this->algo, $this->key) : hash($this->algo, $this->key, \true);
    }
    public function getHash()
    {
        return $this->hashParam;
    }
    public function setHash($hash)
    {
        $this->hashParam = $hash = strtolower($hash);
        switch ($hash) {
            case 'umac-32':
            case 'umac-64':
            case 'umac-96':
            case 'umac-128':
                $this->blockSize = 128;
                $this->length = abs(substr($hash, -3)) >> 3;
                $this->algo = 'umac';
                return;
            case 'md2-96':
            case 'md5-96':
            case 'sha1-96':
            case 'sha224-96':
            case 'sha256-96':
            case 'sha384-96':
            case 'sha512-96':
            case 'sha512/224-96':
            case 'sha512/256-96':
                $hash = substr($hash, 0, -3);
                $this->length = 12;
                break;
            case 'md2':
            case 'md5':
                $this->length = 16;
                break;
            case 'sha1':
                $this->length = 20;
                break;
            case 'sha224':
            case 'sha512/224':
            case 'sha3-224':
                $this->length = 28;
                break;
            case 'keccak256':
                $this->paddingType = self::PADDING_KECCAK;
            case 'sha256':
            case 'sha512/256':
            case 'sha3-256':
                $this->length = 32;
                break;
            case 'sha384':
            case 'sha3-384':
                $this->length = 48;
                break;
            case 'sha512':
            case 'sha3-512':
                $this->length = 64;
                break;
            default:
                if (preg_match('#^(shake(?:128|256))-(\d+)$#', $hash, $matches)) {
                    $this->paddingType = self::PADDING_SHAKE;
                    $hash = $matches[1];
                    $this->length = $matches[2] >> 3;
                } else {
                    throw new UnsupportedAlgorithmException("{$hash} is not a supported algorithm");
                }
        }
        switch ($hash) {
            case 'md2':
            case 'md2-96':
                $this->blockSize = 128;
                break;
            case 'md5-96':
            case 'sha1-96':
            case 'sha224-96':
            case 'sha256-96':
            case 'md5':
            case 'sha1':
            case 'sha224':
            case 'sha256':
                $this->blockSize = 512;
                break;
            case 'sha3-224':
                $this->blockSize = 1152;
                break;
            case 'sha3-256':
            case 'shake256':
            case 'keccak256':
                $this->blockSize = 1088;
                break;
            case 'sha3-384':
                $this->blockSize = 832;
                break;
            case 'sha3-512':
                $this->blockSize = 576;
                break;
            case 'shake128':
                $this->blockSize = 1344;
                break;
            default:
                $this->blockSize = 1024;
        }
        if (in_array(substr($hash, 0, 5), ['sha3-', 'shake', 'kecca'])) {
            if (version_compare(\PHP_VERSION, '7.1.0') < 0 || substr($hash, 0, 5) != 'sha3-') {
                if (!$this->paddingType) {
                    $this->paddingType = self::PADDING_SHA3;
                }
                $this->parameters = ['capacity' => 1600 - $this->blockSize, 'rate' => $this->blockSize, 'length' => $this->length, 'padding' => $this->paddingType];
                $hash = ['Staatic\Vendor\phpseclib3\Crypt\Hash', (\PHP_INT_SIZE == 8) ? 'sha3_64' : 'sha3_32'];
            }
        }
        if ($hash == 'sha512/224' || $hash == 'sha512/256') {
            if (version_compare(\PHP_VERSION, '7.1.0') < 0) {
                $initial = ($hash == 'sha512/256') ? ['22312194FC2BF72C', '9F555FA3C84C64C2', '2393B86B6F53B151', '963877195940EABD', '96283EE2A88EFFE3', 'BE5E1E2553863992', '2B0199FC2C85B8AA', '0EB72DDC81C52CA2'] : ['8C3D37C819544DA2', '73E1996689DCD4D6', '1DFAB7AE32FF9C82', '679DD514582F9FCF', '0F6D2B697BD44DA8', '77E36F7304C48942', '3F9D85A86A1D36C8', '1112E6AD91D692A1'];
                for ($i = 0; $i < 8; $i++) {
                    $initial[$i] = new BigInteger($initial[$i], 16);
                    $initial[$i]->setPrecision(64);
                }
                $this->parameters = compact('initial');
                $hash = ['Staatic\Vendor\phpseclib3\Crypt\Hash', 'sha512'];
            }
        }
        if (is_array($hash)) {
            $b = $this->blockSize >> 3;
            $this->ipad = str_repeat(chr(0x36), $b);
            $this->opad = str_repeat(chr(0x5c), $b);
        }
        $this->algo = $hash;
        $this->computeKey();
    }
    private function kdf($index, $numbytes)
    {
        $this->c->setIV(pack('N4', 0, $index, 0, 1));
        return $this->c->encrypt(str_repeat("\x00", $numbytes));
    }
    private function pdf()
    {
        $k = $this->key;
        $nonce = $this->nonce;
        $taglen = $this->length;
        if ($taglen <= 8) {
            $last = strlen($nonce) - 1;
            $mask = ($taglen == 4) ? "\x03" : "\x01";
            $index = $nonce[$last] & $mask;
            $nonce[$last] = $nonce[$last] ^ $index;
        }
        $nonce = str_pad($nonce, 16, "\x00");
        $kp = $this->kdf(0, 16);
        $c = new AES('ctr');
        $c->disablePadding();
        $c->setKey($kp);
        $c->setIV($nonce);
        $t = $c->encrypt("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00");
        return ($taglen <= 8) ? substr($t, unpack('C', $index)[1] * $taglen, $taglen) : substr($t, 0, $taglen);
    }
    private function uhash($m, $taglen)
    {
        $iters = $taglen >> 2;
        $L1Key = $this->kdf(1, (1024 + ($iters - 1)) * 16);
        $L2Key = $this->kdf(2, $iters * 24);
        $L3Key1 = $this->kdf(3, $iters * 64);
        $L3Key2 = $this->kdf(4, $iters * 4);
        $y = '';
        for ($i = 0; $i < $iters; $i++) {
            $L1Key_i = substr($L1Key, $i * 16, 1024);
            $L2Key_i = substr($L2Key, $i * 24, 24);
            $L3Key1_i = substr($L3Key1, $i * 64, 64);
            $L3Key2_i = substr($L3Key2, $i * 4, 4);
            $a = self::L1Hash($L1Key_i, $m);
            $b = (strlen($m) <= 1024) ? "\x00\x00\x00\x00\x00\x00\x00\x00{$a}" : self::L2Hash($L2Key_i, $a);
            $c = self::L3Hash($L3Key1_i, $L3Key2_i, $b);
            $y .= $c;
        }
        return $y;
    }
    private static function L1Hash($k, $m)
    {
        $m = str_split($m, 1024);
        $length = new BigInteger(1024 * 8);
        $y = '';
        for ($i = 0; $i < count($m) - 1; $i++) {
            $m[$i] = pack('N*', ...unpack('V*', $m[$i]));
            $y .= static::nh($k, $m[$i], $length);
        }
        $length = count($m) ? strlen($m[$i]) : 0;
        $pad = 32 - $length % 32;
        $pad = max(32, $length + $pad % 32);
        $m[$i] = str_pad(isset($m[$i]) ? $m[$i] : '', $pad, "\x00");
        $m[$i] = pack('N*', ...unpack('V*', $m[$i]));
        $y .= static::nh($k, $m[$i], new BigInteger($length * 8));
        return $y;
    }
    private static function nh($k, $m, $length)
    {
        $toUInt32 = function ($x) {
            $x = new BigInteger($x, 256);
            $x->setPrecision(32);
            return $x;
        };
        $m = str_split($m, 4);
        $t = count($m);
        $k = str_split($k, 4);
        $k = array_pad(array_slice($k, 0, $t), $t, 0);
        $m = array_map($toUInt32, $m);
        $k = array_map($toUInt32, $k);
        $y = new BigInteger();
        $y->setPrecision(64);
        $i = 0;
        while ($i < $t) {
            $temp = $m[$i]->add($k[$i]);
            $temp->setPrecision(64);
            $temp = $temp->multiply($m[$i + 4]->add($k[$i + 4]));
            $y = $y->add($temp);
            $temp = $m[$i + 1]->add($k[$i + 1]);
            $temp->setPrecision(64);
            $temp = $temp->multiply($m[$i + 5]->add($k[$i + 5]));
            $y = $y->add($temp);
            $temp = $m[$i + 2]->add($k[$i + 2]);
            $temp->setPrecision(64);
            $temp = $temp->multiply($m[$i + 6]->add($k[$i + 6]));
            $y = $y->add($temp);
            $temp = $m[$i + 3]->add($k[$i + 3]);
            $temp->setPrecision(64);
            $temp = $temp->multiply($m[$i + 7]->add($k[$i + 7]));
            $y = $y->add($temp);
            $i += 8;
        }
        return $y->add($length)->toBytes();
    }
    private static function L2Hash($k, $m)
    {
        $k64 = $k & "\x01\xff\xff\xff\x01\xff\xff\xff";
        $k64 = new BigInteger($k64, 256);
        $k128 = substr($k, 8) & "\x01\xff\xff\xff\x01\xff\xff\xff\x01\xff\xff\xff\x01\xff\xff\xff";
        $k128 = new BigInteger($k128, 256);
        if (strlen($m) <= 0x20000) {
            $y = self::poly(64, self::$maxwordrange64, $k64, $m);
        } else {
            $m_1 = substr($m, 0, 0x20000);
            $m_2 = substr($m, 0x20000) . "\x80";
            $length = strlen($m_2);
            $pad = 16 - $length % 16;
            $pad %= 16;
            $m_2 = str_pad($m_2, $length + $pad, "\x00");
            $y = self::poly(64, self::$maxwordrange64, $k64, $m_1);
            $y = str_pad($y, 16, "\x00", \STR_PAD_LEFT);
            $y = self::poly(128, self::$maxwordrange128, $k128, $y . $m_2);
        }
        return str_pad($y, 16, "\x00", \STR_PAD_LEFT);
    }
    private static function poly($wordbits, $maxwordrange, $k, $m)
    {
        $wordbytes = $wordbits >> 3;
        if ($wordbits == 128) {
            $factory = self::$factory128;
            $offset = self::$offset128;
            $marker = self::$marker128;
        } else {
            $factory = self::$factory64;
            $offset = self::$offset64;
            $marker = self::$marker64;
        }
        $k = $factory->newInteger($k);
        $m_i = str_split($m, $wordbytes);
        $y = $factory->newInteger(new BigInteger(1));
        foreach ($m_i as $m) {
            $m = $factory->newInteger(new BigInteger($m, 256));
            if ($m->compare($maxwordrange) >= 0) {
                $y = $k->multiply($y)->add($marker);
                $y = $k->multiply($y)->add($m->subtract($offset));
            } else {
                $y = $k->multiply($y)->add($m);
            }
        }
        return $y->toBytes();
    }
    private static function L3Hash($k1, $k2, $m)
    {
        $factory = self::$factory36;
        $y = $factory->newInteger(new BigInteger());
        for ($i = 0; $i < 8; $i++) {
            $m_i = $factory->newInteger(new BigInteger(substr($m, 2 * $i, 2), 256));
            $k_i = $factory->newInteger(new BigInteger(substr($k1, 8 * $i, 8), 256));
            $y = $y->add($m_i->multiply($k_i));
        }
        $y = str_pad(substr($y->toBytes(), -4), 4, "\x00", \STR_PAD_LEFT);
        $y = $y ^ $k2;
        return $y;
    }
    public function hash($text)
    {
        $algo = $this->algo;
        if ($algo == 'umac') {
            if ($this->recomputeAESKey) {
                if (!is_string($this->nonce)) {
                    throw new InsufficientSetupException('No nonce has been set');
                }
                if (!is_string($this->key)) {
                    throw new InsufficientSetupException('No key has been set');
                }
                if (strlen($this->key) != 16) {
                    throw new LengthException('Key must be 16 bytes long');
                }
                if (!isset(self::$maxwordrange64)) {
                    $one = new BigInteger(1);
                    $prime36 = new BigInteger("\x00\x00\x00\x0f\xff\xff\xff\xfb", 256);
                    self::$factory36 = new PrimeField($prime36);
                    $prime64 = new BigInteger("\xff\xff\xff\xff\xff\xff\xff\xc5", 256);
                    self::$factory64 = new PrimeField($prime64);
                    $prime128 = new BigInteger("\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffa", 256);
                    self::$factory128 = new PrimeField($prime128);
                    self::$offset64 = new BigInteger("\x01\x00\x00\x00\x00\x00\x00\x00\x00", 256);
                    self::$offset64 = self::$factory64->newInteger(self::$offset64->subtract($prime64));
                    self::$offset128 = new BigInteger("\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", 256);
                    self::$offset128 = self::$factory128->newInteger(self::$offset128->subtract($prime128));
                    self::$marker64 = self::$factory64->newInteger($prime64->subtract($one));
                    self::$marker128 = self::$factory128->newInteger($prime128->subtract($one));
                    $maxwordrange64 = $one->bitwise_leftShift(64)->subtract($one->bitwise_leftShift(32));
                    self::$maxwordrange64 = self::$factory64->newInteger($maxwordrange64);
                    $maxwordrange128 = $one->bitwise_leftShift(128)->subtract($one->bitwise_leftShift(96));
                    self::$maxwordrange128 = self::$factory128->newInteger($maxwordrange128);
                }
                $this->c = new AES('ctr');
                $this->c->disablePadding();
                $this->c->setKey($this->key);
                $this->pad = $this->pdf();
                $this->recomputeAESKey = \false;
            }
            $hashedmessage = $this->uhash($text, $this->length);
            return $hashedmessage ^ $this->pad;
        }
        if (is_array($algo)) {
            if (empty($this->key) || !is_string($this->key)) {
                return substr($algo($text, ...array_values($this->parameters)), 0, $this->length);
            }
            $key = str_pad($this->computedKey, $b, chr(0));
            $temp = $this->ipad ^ $key;
            $temp .= $text;
            $temp = substr($algo($temp, ...array_values($this->parameters)), 0, $this->length);
            $output = $this->opad ^ $key;
            $output .= $temp;
            $output = $algo($output, ...array_values($this->parameters));
            return substr($output, 0, $this->length);
        }
        $output = (!empty($this->key) || is_string($this->key)) ? hash_hmac($algo, $text, $this->computedKey, \true) : hash($algo, $text, \true);
        return (strlen($output) > $this->length) ? substr($output, 0, $this->length) : $output;
    }
    public function getLength()
    {
        return $this->length << 3;
    }
    public function getLengthInBytes()
    {
        return $this->length;
    }
    public function getBlockLength()
    {
        return $this->blockSize;
    }
    public function getBlockLengthInBytes()
    {
        return $this->blockSize >> 3;
    }
    private static function sha3_pad($padLength, $padType)
    {
        switch ($padType) {
            case self::PADDING_KECCAK:
                $temp = chr(0x1) . str_repeat("\x00", $padLength - 1);
                $temp[$padLength - 1] = $temp[$padLength - 1] | chr(0x80);
                return $temp;
            case self::PADDING_SHAKE:
                $temp = chr(0x1f) . str_repeat("\x00", $padLength - 1);
                $temp[$padLength - 1] = $temp[$padLength - 1] | chr(0x80);
                return $temp;
            default:
                return ($padLength == 1) ? chr(0x86) : (chr(0x6) . str_repeat("\x00", $padLength - 2) . chr(0x80));
        }
    }
    private static function sha3_32($p, $c, $r, $d, $padType)
    {
        $block_size = $r >> 3;
        $padLength = $block_size - strlen($p) % $block_size;
        $num_ints = $block_size >> 2;
        $p .= static::sha3_pad($padLength, $padType);
        $n = strlen($p) / $r;
        $s = [[[0, 0], [0, 0], [0, 0], [0, 0], [0, 0]], [[0, 0], [0, 0], [0, 0], [0, 0], [0, 0]], [[0, 0], [0, 0], [0, 0], [0, 0], [0, 0]], [[0, 0], [0, 0], [0, 0], [0, 0], [0, 0]], [[0, 0], [0, 0], [0, 0], [0, 0], [0, 0]]];
        $p = str_split($p, $block_size);
        foreach ($p as $pi) {
            $pi = unpack('V*', $pi);
            $x = $y = 0;
            for ($i = 1; $i <= $num_ints; $i += 2) {
                $s[$x][$y][0] ^= $pi[$i + 1];
                $s[$x][$y][1] ^= $pi[$i];
                if (++$y == 5) {
                    $y = 0;
                    $x++;
                }
            }
            static::processSHA3Block32($s);
        }
        $z = '';
        $i = $j = 0;
        while (strlen($z) < $d) {
            $z .= pack('V2', $s[$i][$j][1], $s[$i][$j++][0]);
            if ($j == 5) {
                $j = 0;
                $i++;
                if ($i == 5) {
                    $i = 0;
                    static::processSHA3Block32($s);
                }
            }
        }
        return $z;
    }
    private static function processSHA3Block32(&$s)
    {
        static $rotationOffsets = [[0, 1, 62, 28, 27], [36, 44, 6, 55, 20], [3, 10, 43, 25, 39], [41, 45, 15, 21, 8], [18, 2, 61, 56, 14]];
        static $roundConstants = [[0, 1], [0, 32898], [-2147483648, 32906], [-2147483648, -2147450880], [0, 32907], [0, -2147483647], [-2147483648, -2147450751], [-2147483648, 32777], [0, 138], [0, 136], [0, -2147450871], [0, -2147483638], [0, -2147450741], [-2147483648, 139], [-2147483648, 32905], [-2147483648, 32771], [-2147483648, 32770], [-2147483648, 128], [0, 32778], [-2147483648, -2147483638], [-2147483648, -2147450751], [-2147483648, 32896], [0, -2147483647], [-2147483648, -2147450872]];
        for ($round = 0; $round < 24; $round++) {
            $parity = $rotated = [];
            for ($i = 0; $i < 5; $i++) {
                $parity[] = [$s[0][$i][0] ^ $s[1][$i][0] ^ $s[2][$i][0] ^ $s[3][$i][0] ^ $s[4][$i][0], $s[0][$i][1] ^ $s[1][$i][1] ^ $s[2][$i][1] ^ $s[3][$i][1] ^ $s[4][$i][1]];
                $rotated[] = static::rotateLeft32($parity[$i], 1);
            }
            $temp = [[$parity[4][0] ^ $rotated[1][0], $parity[4][1] ^ $rotated[1][1]], [$parity[0][0] ^ $rotated[2][0], $parity[0][1] ^ $rotated[2][1]], [$parity[1][0] ^ $rotated[3][0], $parity[1][1] ^ $rotated[3][1]], [$parity[2][0] ^ $rotated[4][0], $parity[2][1] ^ $rotated[4][1]], [$parity[3][0] ^ $rotated[0][0], $parity[3][1] ^ $rotated[0][1]]];
            for ($i = 0; $i < 5; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    $s[$i][$j][0] ^= $temp[$j][0];
                    $s[$i][$j][1] ^= $temp[$j][1];
                }
            }
            $st = $s;
            for ($i = 0; $i < 5; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    $st[(2 * $i + 3 * $j) % 5][$j] = static::rotateLeft32($s[$j][$i], $rotationOffsets[$j][$i]);
                }
            }
            for ($i = 0; $i < 5; $i++) {
                $s[$i][0] = [$st[$i][0][0] ^ ~$st[$i][1][0] & $st[$i][2][0], $st[$i][0][1] ^ ~$st[$i][1][1] & $st[$i][2][1]];
                $s[$i][1] = [$st[$i][1][0] ^ ~$st[$i][2][0] & $st[$i][3][0], $st[$i][1][1] ^ ~$st[$i][2][1] & $st[$i][3][1]];
                $s[$i][2] = [$st[$i][2][0] ^ ~$st[$i][3][0] & $st[$i][4][0], $st[$i][2][1] ^ ~$st[$i][3][1] & $st[$i][4][1]];
                $s[$i][3] = [$st[$i][3][0] ^ ~$st[$i][4][0] & $st[$i][0][0], $st[$i][3][1] ^ ~$st[$i][4][1] & $st[$i][0][1]];
                $s[$i][4] = [$st[$i][4][0] ^ ~$st[$i][0][0] & $st[$i][1][0], $st[$i][4][1] ^ ~$st[$i][0][1] & $st[$i][1][1]];
            }
            $s[0][0][0] ^= $roundConstants[$round][0];
            $s[0][0][1] ^= $roundConstants[$round][1];
        }
    }
    private static function rotateLeft32($x, $shift)
    {
        if ($shift < 32) {
            list($hi, $lo) = $x;
        } else {
            $shift -= 32;
            list($lo, $hi) = $x;
        }
        return [$hi << $shift | $lo >> 32 - $shift & (1 << $shift) - 1, $lo << $shift | $hi >> 32 - $shift & (1 << $shift) - 1];
    }
    private static function sha3_64($p, $c, $r, $d, $padType)
    {
        $block_size = $r >> 3;
        $padLength = $block_size - strlen($p) % $block_size;
        $num_ints = $block_size >> 2;
        $p .= static::sha3_pad($padLength, $padType);
        $n = strlen($p) / $r;
        $s = [[0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0]];
        $p = str_split($p, $block_size);
        foreach ($p as $pi) {
            $pi = unpack('P*', $pi);
            $x = $y = 0;
            foreach ($pi as $subpi) {
                $s[$x][$y++] ^= $subpi;
                if ($y == 5) {
                    $y = 0;
                    $x++;
                }
            }
            static::processSHA3Block64($s);
        }
        $z = '';
        $i = $j = 0;
        while (strlen($z) < $d) {
            $z .= pack('P', $s[$i][$j++]);
            if ($j == 5) {
                $j = 0;
                $i++;
                if ($i == 5) {
                    $i = 0;
                    static::processSHA3Block64($s);
                }
            }
        }
        return $z;
    }
    private static function processSHA3Block64(&$s)
    {
        static $rotationOffsets = [[0, 1, 62, 28, 27], [36, 44, 6, 55, 20], [3, 10, 43, 25, 39], [41, 45, 15, 21, 8], [18, 2, 61, 56, 14]];
        static $roundConstants = [1, 32898, -9223372036854742902, -9223372034707259392, 32907, 2147483649, -9223372034707259263, -9223372036854743031, 138, 136, 2147516425, 2147483658, 2147516555, -9223372036854775669, -9223372036854742903, -9223372036854743037, -9223372036854743038, -9223372036854775680, 32778, -9223372034707292150, -9223372034707259263, -9223372036854742912, 2147483649, -9223372034707259384];
        for ($round = 0; $round < 24; $round++) {
            $parity = [];
            for ($i = 0; $i < 5; $i++) {
                $parity[] = $s[0][$i] ^ $s[1][$i] ^ $s[2][$i] ^ $s[3][$i] ^ $s[4][$i];
            }
            $temp = [$parity[4] ^ static::rotateLeft64($parity[1], 1), $parity[0] ^ static::rotateLeft64($parity[2], 1), $parity[1] ^ static::rotateLeft64($parity[3], 1), $parity[2] ^ static::rotateLeft64($parity[4], 1), $parity[3] ^ static::rotateLeft64($parity[0], 1)];
            for ($i = 0; $i < 5; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    $s[$i][$j] ^= $temp[$j];
                }
            }
            $st = $s;
            for ($i = 0; $i < 5; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    $st[(2 * $i + 3 * $j) % 5][$j] = static::rotateLeft64($s[$j][$i], $rotationOffsets[$j][$i]);
                }
            }
            for ($i = 0; $i < 5; $i++) {
                $s[$i] = [$st[$i][0] ^ ~$st[$i][1] & $st[$i][2], $st[$i][1] ^ ~$st[$i][2] & $st[$i][3], $st[$i][2] ^ ~$st[$i][3] & $st[$i][4], $st[$i][3] ^ ~$st[$i][4] & $st[$i][0], $st[$i][4] ^ ~$st[$i][0] & $st[$i][1]];
            }
            $s[0][0] ^= $roundConstants[$round];
        }
    }
    private static function rotateLeft64($x, $shift)
    {
        return $x << $shift | $x >> 64 - $shift & (1 << $shift) - 1;
    }
    private static function sha512($m, $hash)
    {
        static $k;
        if (!isset($k)) {
            $k = ['428a2f98d728ae22', '7137449123ef65cd', 'b5c0fbcfec4d3b2f', 'e9b5dba58189dbbc', '3956c25bf348b538', '59f111f1b605d019', '923f82a4af194f9b', 'ab1c5ed5da6d8118', 'd807aa98a3030242', '12835b0145706fbe', '243185be4ee4b28c', '550c7dc3d5ffb4e2', '72be5d74f27b896f', '80deb1fe3b1696b1', '9bdc06a725c71235', 'c19bf174cf692694', 'e49b69c19ef14ad2', 'efbe4786384f25e3', '0fc19dc68b8cd5b5', '240ca1cc77ac9c65', '2de92c6f592b0275', '4a7484aa6ea6e483', '5cb0a9dcbd41fbd4', '76f988da831153b5', '983e5152ee66dfab', 'a831c66d2db43210', 'b00327c898fb213f', 'bf597fc7beef0ee4', 'c6e00bf33da88fc2', 'd5a79147930aa725', '06ca6351e003826f', '142929670a0e6e70', '27b70a8546d22ffc', '2e1b21385c26c926', '4d2c6dfc5ac42aed', '53380d139d95b3df', '650a73548baf63de', '766a0abb3c77b2a8', '81c2c92e47edaee6', '92722c851482353b', 'a2bfe8a14cf10364', 'a81a664bbc423001', 'c24b8b70d0f89791', 'c76c51a30654be30', 'd192e819d6ef5218', 'd69906245565a910', 'f40e35855771202a', '106aa07032bbd1b8', '19a4c116b8d2d0c8', '1e376c085141ab53', '2748774cdf8eeb99', '34b0bcb5e19b48a8', '391c0cb3c5c95a63', '4ed8aa4ae3418acb', '5b9cca4f7763e373', '682e6ff3d6b2b8a3', '748f82ee5defb2fc', '78a5636f43172f60', '84c87814a1f0ab72', '8cc702081a6439ec', '90befffa23631e28', 'a4506cebde82bde9', 'bef9a3f7b2c67915', 'c67178f2e372532b', 'ca273eceea26619c', 'd186b8c721c0c207', 'eada7dd6cde0eb1e', 'f57d4f7fee6ed178', '06f067aa72176fba', '0a637dc5a2c898a6', '113f9804bef90dae', '1b710b35131c471b', '28db77f523047d84', '32caab7b40c72493', '3c9ebe0a15c9bebc', '431d67c49c100d4c', '4cc5d4becb3e42b6', '597f299cfc657e2a', '5fcb6fab3ad6faec', '6c44198c4a475817'];
            for ($i = 0; $i < 80; $i++) {
                $k[$i] = new BigInteger($k[$i], 16);
            }
        }
        $length = strlen($m);
        $m .= str_repeat(chr(0), 128 - ($length + 16 & 0x7f));
        $m[$length] = chr(0x80);
        $m .= pack('N4', 0, 0, 0, $length << 3);
        $chunks = str_split($m, 128);
        foreach ($chunks as $chunk) {
            $w = [];
            for ($i = 0; $i < 16; $i++) {
                $temp = new BigInteger(Strings::shift($chunk, 8), 256);
                $temp->setPrecision(64);
                $w[] = $temp;
            }
            for ($i = 16; $i < 80; $i++) {
                $temp = [$w[$i - 15]->bitwise_rightRotate(1), $w[$i - 15]->bitwise_rightRotate(8), $w[$i - 15]->bitwise_rightShift(7)];
                $s0 = $temp[0]->bitwise_xor($temp[1]);
                $s0 = $s0->bitwise_xor($temp[2]);
                $temp = [$w[$i - 2]->bitwise_rightRotate(19), $w[$i - 2]->bitwise_rightRotate(61), $w[$i - 2]->bitwise_rightShift(6)];
                $s1 = $temp[0]->bitwise_xor($temp[1]);
                $s1 = $s1->bitwise_xor($temp[2]);
                $w[$i] = clone $w[$i - 16];
                $w[$i] = $w[$i]->add($s0);
                $w[$i] = $w[$i]->add($w[$i - 7]);
                $w[$i] = $w[$i]->add($s1);
            }
            $a = clone $hash[0];
            $b = clone $hash[1];
            $c = clone $hash[2];
            $d = clone $hash[3];
            $e = clone $hash[4];
            $f = clone $hash[5];
            $g = clone $hash[6];
            $h = clone $hash[7];
            for ($i = 0; $i < 80; $i++) {
                $temp = [$a->bitwise_rightRotate(28), $a->bitwise_rightRotate(34), $a->bitwise_rightRotate(39)];
                $s0 = $temp[0]->bitwise_xor($temp[1]);
                $s0 = $s0->bitwise_xor($temp[2]);
                $temp = [$a->bitwise_and($b), $a->bitwise_and($c), $b->bitwise_and($c)];
                $maj = $temp[0]->bitwise_xor($temp[1]);
                $maj = $maj->bitwise_xor($temp[2]);
                $t2 = $s0->add($maj);
                $temp = [$e->bitwise_rightRotate(14), $e->bitwise_rightRotate(18), $e->bitwise_rightRotate(41)];
                $s1 = $temp[0]->bitwise_xor($temp[1]);
                $s1 = $s1->bitwise_xor($temp[2]);
                $temp = [$e->bitwise_and($f), $g->bitwise_and($e->bitwise_not())];
                $ch = $temp[0]->bitwise_xor($temp[1]);
                $t1 = $h->add($s1);
                $t1 = $t1->add($ch);
                $t1 = $t1->add($k[$i]);
                $t1 = $t1->add($w[$i]);
                $h = clone $g;
                $g = clone $f;
                $f = clone $e;
                $e = $d->add($t1);
                $d = clone $c;
                $c = clone $b;
                $b = clone $a;
                $a = $t1->add($t2);
            }
            $hash = [$hash[0]->add($a), $hash[1]->add($b), $hash[2]->add($c), $hash[3]->add($d), $hash[4]->add($e), $hash[5]->add($f), $hash[6]->add($g), $hash[7]->add($h)];
        }
        $temp = $hash[0]->toBytes() . $hash[1]->toBytes() . $hash[2]->toBytes() . $hash[3]->toBytes() . $hash[4]->toBytes() . $hash[5]->toBytes() . $hash[6]->toBytes() . $hash[7]->toBytes();
        return $temp;
    }
    public function __toString()
    {
        return $this->getHash();
    }
}
