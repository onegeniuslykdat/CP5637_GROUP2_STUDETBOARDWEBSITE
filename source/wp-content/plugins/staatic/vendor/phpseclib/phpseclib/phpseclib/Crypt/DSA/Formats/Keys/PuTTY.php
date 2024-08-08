<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys;

use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PuTTY as Progenitor;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PuTTY extends Progenitor
{
    const PUBLIC_HANDLER = 'Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys\OpenSSH';
    protected static $types = ['ssh-dss'];
    public static function load($key, $password = '')
    {
        $components = parent::load($key, $password);
        if (!isset($components['private'])) {
            return $components;
        }
        extract($components);
        unset($components['public'], $components['private']);
        list($p, $q, $g, $y) = Strings::unpackSSH2('iiii', $public);
        list($x) = Strings::unpackSSH2('i', $private);
        return compact('p', 'q', 'g', 'y', 'x', 'comment');
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     * @param BigInteger $x
     * @param mixed[] $options
     */
    public static function savePrivateKey($p, $q, $g, $y, $x, $password = \false, $options = [])
    {
        if ($q->getLength() != 160) {
            throw new InvalidArgumentException('SSH only supports keys with an N (length of Group Order q) of 160');
        }
        $public = Strings::packSSH2('iiii', $p, $q, $g, $y);
        $private = Strings::packSSH2('i', $x);
        return self::wrapPrivateKey($public, $private, 'ssh-dss', $password, $options);
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     */
    public static function savePublicKey($p, $q, $g, $y)
    {
        if ($q->getLength() != 160) {
            throw new InvalidArgumentException('SSH only supports keys with an N (length of Group Order q) of 160');
        }
        return self::wrapPublicKey(Strings::packSSH2('iiii', $p, $q, $g, $y), 'ssh-dss');
    }
}
