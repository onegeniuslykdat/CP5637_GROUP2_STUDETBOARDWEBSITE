<?php

namespace Staatic\Vendor\phpseclib3\System\SSH;

use Staatic\Vendor\phpseclib3\System\SSH\Common\Traits\ReadBytes;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\PublicKeyLoader;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
use Staatic\Vendor\phpseclib3\Net\SSH2;
use Staatic\Vendor\phpseclib3\System\SSH\Agent\Identity;
class Agent
{
    use ReadBytes;
    const SSH_AGENTC_REQUEST_IDENTITIES = 11;
    const SSH_AGENT_IDENTITIES_ANSWER = 12;
    const SSH_AGENTC_SIGN_REQUEST = 13;
    const SSH_AGENT_SIGN_RESPONSE = 14;
    const FORWARD_NONE = 0;
    const FORWARD_REQUEST = 1;
    const FORWARD_ACTIVE = 2;
    const SSH_AGENT_FAILURE = 5;
    private $fsock;
    private $forward_status = self::FORWARD_NONE;
    private $socket_buffer = '';
    private $expected_bytes = 0;
    public function __construct($address = null)
    {
        if (!$address) {
            switch (\true) {
                case isset($_SERVER['SSH_AUTH_SOCK']):
                    $address = $_SERVER['SSH_AUTH_SOCK'];
                    break;
                case isset($_ENV['SSH_AUTH_SOCK']):
                    $address = $_ENV['SSH_AUTH_SOCK'];
                    break;
                default:
                    throw new BadConfigurationException('SSH_AUTH_SOCK not found');
            }
        }
        if (in_array('unix', stream_get_transports())) {
            $this->fsock = fsockopen('unix://' . $address, 0, $errno, $errstr);
            if (!$this->fsock) {
                throw new RuntimeException("Unable to connect to ssh-agent (Error {$errno}: {$errstr})");
            }
        } else {
            if (substr($address, 0, 9) != '\\\\.\pipe\\' || strpos(substr($address, 9), '\\') !== \false) {
                throw new RuntimeException('Address is not formatted as a named pipe should be');
            }
            $this->fsock = fopen($address, 'r+b');
            if (!$this->fsock) {
                throw new RuntimeException('Unable to open address');
            }
        }
    }
    public function requestIdentities()
    {
        if (!$this->fsock) {
            return [];
        }
        $packet = pack('NC', 1, self::SSH_AGENTC_REQUEST_IDENTITIES);
        if (strlen($packet) != fputs($this->fsock, $packet)) {
            throw new RuntimeException('Connection closed while requesting identities');
        }
        $length = current(unpack('N', $this->readBytes(4)));
        $packet = $this->readBytes($length);
        list($type, $keyCount) = Strings::unpackSSH2('CN', $packet);
        if ($type != self::SSH_AGENT_IDENTITIES_ANSWER) {
            throw new RuntimeException('Unable to request identities');
        }
        $identities = [];
        for ($i = 0; $i < $keyCount; $i++) {
            list($key_blob, $comment) = Strings::unpackSSH2('ss', $packet);
            $temp = $key_blob;
            list($key_type) = Strings::unpackSSH2('s', $temp);
            switch ($key_type) {
                case 'ssh-rsa':
                case 'ssh-dss':
                case 'ssh-ed25519':
                case 'ecdsa-sha2-nistp256':
                case 'ecdsa-sha2-nistp384':
                case 'ecdsa-sha2-nistp521':
                    $key = PublicKeyLoader::load($key_type . ' ' . base64_encode($key_blob));
            }
            if (isset($key)) {
                $identity = (new Identity($this->fsock))->withPublicKey($key)->withPublicKeyBlob($key_blob);
                $identities[] = $identity;
                unset($key);
            }
        }
        return $identities;
    }
    public function startSSHForwarding()
    {
        if ($this->forward_status == self::FORWARD_NONE) {
            $this->forward_status = self::FORWARD_REQUEST;
        }
    }
    private function request_forwarding(SSH2 $ssh)
    {
        if (!$ssh->requestAgentForwarding()) {
            return \false;
        }
        $this->forward_status = self::FORWARD_ACTIVE;
        return \true;
    }
    /**
     * @param SSH2 $ssh
     */
    public function registerChannelOpen($ssh)
    {
        if ($this->forward_status == self::FORWARD_REQUEST) {
            $this->request_forwarding($ssh);
        }
    }
    public function forwardData($data)
    {
        if ($this->expected_bytes > 0) {
            $this->socket_buffer .= $data;
            $this->expected_bytes -= strlen($data);
        } else {
            $agent_data_bytes = current(unpack('N', $data));
            $current_data_bytes = strlen($data);
            $this->socket_buffer = $data;
            if ($current_data_bytes != $agent_data_bytes + 4) {
                $this->expected_bytes = $agent_data_bytes + 4 - $current_data_bytes;
                return \false;
            }
        }
        if (strlen($this->socket_buffer) != fwrite($this->fsock, $this->socket_buffer)) {
            throw new RuntimeException('Connection closed attempting to forward data to SSH agent');
        }
        $this->socket_buffer = '';
        $this->expected_bytes = 0;
        $agent_reply_bytes = current(unpack('N', $this->readBytes(4)));
        $agent_reply_data = $this->readBytes($agent_reply_bytes);
        $agent_reply_data = current(unpack('a*', $agent_reply_data));
        return pack('Na*', $agent_reply_bytes, $agent_reply_data);
    }
}
