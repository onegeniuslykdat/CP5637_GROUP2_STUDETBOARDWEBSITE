<?php

namespace Staatic\Vendor\phpseclib3\System\SSH\Agent;

use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\PrivateKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\PublicKey;
use Staatic\Vendor\phpseclib3\Crypt\DSA;
use Staatic\Vendor\phpseclib3\Crypt\EC;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\System\SSH\Agent;
use Staatic\Vendor\phpseclib3\System\SSH\Common\Traits\ReadBytes;
class Identity implements PrivateKey
{
    use ReadBytes;
    const SSH_AGENT_RSA2_256 = 2;
    const SSH_AGENT_RSA2_512 = 4;
    private $key;
    private $key_blob;
    private $fsock;
    private $flags = 0;
    private static $curveAliases = ['secp256r1' => 'nistp256', 'secp384r1' => 'nistp384', 'secp521r1' => 'nistp521', 'Ed25519' => 'Ed25519'];
    public function __construct($fsock)
    {
        $this->fsock = $fsock;
    }
    /**
     * @param PublicKey $key
     */
    public function withPublicKey($key)
    {
        if ($key instanceof EC) {
            if (is_array($key->getCurve()) || !isset(self::$curveAliases[$key->getCurve()])) {
                throw new UnsupportedAlgorithmException('The only supported curves are nistp256, nistp384, nistp512 and Ed25519');
            }
        }
        $new = clone $this;
        $new->key = $key;
        return $new;
    }
    public function withPublicKeyBlob($key_blob)
    {
        $new = clone $this;
        $new->key_blob = $key_blob;
        return $new;
    }
    public function getPublicKey($type = 'PKCS8')
    {
        return $this->key;
    }
    public function withHash($hash)
    {
        $new = clone $this;
        $hash = strtolower($hash);
        if ($this->key instanceof RSA) {
            $new->flags = 0;
            switch ($hash) {
                case 'sha1':
                    break;
                case 'sha256':
                    $new->flags = self::SSH_AGENT_RSA2_256;
                    break;
                case 'sha512':
                    $new->flags = self::SSH_AGENT_RSA2_512;
                    break;
                default:
                    throw new UnsupportedAlgorithmException('The only supported hashes for RSA are sha1, sha256 and sha512');
            }
        }
        if ($this->key instanceof EC) {
            switch ($this->key->getCurve()) {
                case 'secp256r1':
                    $expectedHash = 'sha256';
                    break;
                case 'secp384r1':
                    $expectedHash = 'sha384';
                    break;
                default:
                    $expectedHash = 'sha512';
            }
            if ($hash != $expectedHash) {
                throw new UnsupportedAlgorithmException('The only supported hash for ' . self::$curveAliases[$this->key->getCurve()] . ' is ' . $expectedHash);
            }
        }
        if ($this->key instanceof DSA) {
            if ($hash != 'sha1') {
                throw new UnsupportedAlgorithmException('The only supported hash for DSA is sha1');
            }
        }
        return $new;
    }
    public function withPadding($padding)
    {
        if (!$this->key instanceof RSA) {
            throw new UnsupportedAlgorithmException('Only RSA keys support padding');
        }
        if ($padding != RSA::SIGNATURE_PKCS1 && $padding != RSA::SIGNATURE_RELAXED_PKCS1) {
            throw new UnsupportedAlgorithmException('ssh-agent can only create PKCS1 signatures');
        }
        return $this;
    }
    public function withSignatureFormat($format)
    {
        if ($this->key instanceof RSA) {
            throw new UnsupportedAlgorithmException('Only DSA and EC keys support signature format setting');
        }
        if ($format != 'SSH2') {
            throw new UnsupportedAlgorithmException('Only SSH2-formatted signatures are currently supported');
        }
        return $this;
    }
    public function getCurve()
    {
        if (!$this->key instanceof EC) {
            throw new UnsupportedAlgorithmException('Only EC keys have curves');
        }
        return $this->key->getCurve();
    }
    public function sign($message)
    {
        $packet = Strings::packSSH2('CssN', Agent::SSH_AGENTC_SIGN_REQUEST, $this->key_blob, $message, $this->flags);
        $packet = Strings::packSSH2('s', $packet);
        if (strlen($packet) != fputs($this->fsock, $packet)) {
            throw new RuntimeException('Connection closed during signing');
        }
        $length = current(unpack('N', $this->readBytes(4)));
        $packet = $this->readBytes($length);
        list($type, $signature_blob) = Strings::unpackSSH2('Cs', $packet);
        if ($type != Agent::SSH_AGENT_SIGN_RESPONSE) {
            throw new RuntimeException('Unable to retrieve signature');
        }
        if (!$this->key instanceof RSA) {
            return $signature_blob;
        }
        list($type, $signature_blob) = Strings::unpackSSH2('ss', $signature_blob);
        return $signature_blob;
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        throw new RuntimeException('ssh-agent does not provide a mechanism to get the private key');
    }
    public function withPassword($password = \false)
    {
        throw new RuntimeException('ssh-agent does not provide a mechanism to get the private key');
    }
}
