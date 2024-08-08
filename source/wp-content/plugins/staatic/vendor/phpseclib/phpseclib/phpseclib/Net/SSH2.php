<?php

namespace Staatic\Vendor\phpseclib3\Net;

use WeakReference;
use RuntimeException;
use UnexpectedValueException;
use LengthException;
use stdClass;
use Exception;
use InvalidArgumentException;
use LogicException;
use ReturnTypeWillChange;
use const Staatic\Vendor\NET_SSH2_MSG_KEXINIT;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_PROTOCOL_ERROR;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED;
use const Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_GROUP;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_HOST_KEY_NOT_VERIFIABLE;
use const Staatic\Vendor\NET_SSH2_MSG_NEWKEYS;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_CONNECTION_LOST;
use const Staatic\Vendor\NET_SSH2_MSG_SERVICE_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_SERVICE_ACCEPT;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_SUCCESS;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_FAILURE;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PASSWD_CHANGEREQ;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_AUTH_CANCELLED_BY_USER;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_RESPONSE;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PK_OK;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_BY_APPLICATION;
use const Staatic\Vendor\NET_SSH2_TTY_OP_END;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_DATA;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_CLOSE;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN;
use const Staatic\Vendor\NET_SSH2_MSG_IGNORE;
use const Staatic\Vendor\NET_SSH2_DISCONNECT_MAC_ERROR;
use const Staatic\Vendor\NET_SSH2_MSG_DISCONNECT;
use const Staatic\Vendor\NET_SSH2_MSG_DEBUG;
use const Staatic\Vendor\NET_SSH2_MSG_UNIMPLEMENTED;
use const Staatic\Vendor\NET_SSH2_MSG_EXT_INFO;
use const Staatic\Vendor\NET_SSH2_MSG_USERAUTH_BANNER;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_SUCCESS;
use const Staatic\Vendor\NET_SSH2_MSG_GLOBAL_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_REQUEST_FAILURE;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN_CONFIRMATION;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN_FAILURE;
use const Staatic\Vendor\NET_SSH2_OPEN_ADMINISTRATIVELY_PROHIBITED;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_WINDOW_ADJUST;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_FAILURE;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_EXTENDED_DATA;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_EOF;
use const Staatic\Vendor\NET_SSH2_LOGGING;
use const Staatic\Vendor\NET_SSH2_LOG_REALTIME_FILENAME;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Blowfish;
use Staatic\Vendor\phpseclib3\Crypt\ChaCha20;
use Staatic\Vendor\phpseclib3\Crypt\Common\AsymmetricKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\PrivateKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\PublicKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\SymmetricKey;
use Staatic\Vendor\phpseclib3\Crypt\DH;
use Staatic\Vendor\phpseclib3\Crypt\DSA;
use Staatic\Vendor\phpseclib3\Crypt\EC;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Crypt\RC4;
use Staatic\Vendor\phpseclib3\Crypt\Rijndael;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Crypt\TripleDES;
use Staatic\Vendor\phpseclib3\Crypt\Twofish;
use Staatic\Vendor\phpseclib3\Exception\ConnectionClosedException;
use Staatic\Vendor\phpseclib3\Exception\InsufficientSetupException;
use Staatic\Vendor\phpseclib3\Exception\InvalidPacketLengthException;
use Staatic\Vendor\phpseclib3\Exception\NoSupportedAlgorithmsException;
use Staatic\Vendor\phpseclib3\Exception\UnableToConnectException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\System\SSH\Agent;
class SSH2
{
    const NET_SSH2_COMPRESSION_NONE = 1;
    const NET_SSH2_COMPRESSION_ZLIB = 2;
    const NET_SSH2_COMPRESSION_ZLIB_AT_OPENSSH = 3;
    const MASK_CONSTRUCTOR = 0x1;
    const MASK_CONNECTED = 0x2;
    const MASK_LOGIN_REQ = 0x4;
    const MASK_LOGIN = 0x8;
    const MASK_SHELL = 0x10;
    const MASK_WINDOW_ADJUST = 0x20;
    const CHANNEL_EXEC = 1;
    const CHANNEL_SHELL = 2;
    const CHANNEL_SUBSYSTEM = 3;
    const CHANNEL_AGENT_FORWARD = 4;
    const CHANNEL_KEEP_ALIVE = 5;
    const LOG_SIMPLE = 1;
    const LOG_COMPLEX = 2;
    const LOG_REALTIME = 3;
    const LOG_REALTIME_FILE = 4;
    const LOG_SIMPLE_REALTIME = 5;
    const LOG_MAX_SIZE = 1048576;
    const READ_SIMPLE = 1;
    const READ_REGEX = 2;
    const READ_NEXT = 3;
    private $identifier;
    public $fsock;
    protected $bitmap = 0;
    private $errors = [];
    protected $server_identifier = \false;
    private $kex_algorithms = \false;
    private $kex_algorithm = \false;
    private $kex_dh_group_size_min = 1536;
    private $kex_dh_group_size_preferred = 2048;
    private $kex_dh_group_size_max = 4096;
    private $server_host_key_algorithms = \false;
    private $supported_private_key_algorithms = \false;
    private $encryption_algorithms_client_to_server = \false;
    private $encryption_algorithms_server_to_client = \false;
    private $mac_algorithms_client_to_server = \false;
    private $mac_algorithms_server_to_client = \false;
    private $compression_algorithms_client_to_server = \false;
    private $compression_algorithms_server_to_client = \false;
    private $languages_server_to_client = \false;
    private $languages_client_to_server = \false;
    private $preferred = [];
    private $encrypt_block_size = 8;
    private $decrypt_block_size = 8;
    private $decrypt = \false;
    private $decryptName;
    private $decryptInvocationCounter;
    private $decryptFixedPart;
    private $lengthDecrypt = \false;
    private $encrypt = \false;
    private $encryptName;
    private $encryptInvocationCounter;
    private $encryptFixedPart;
    private $lengthEncrypt = \false;
    private $hmac_create = \false;
    private $hmac_create_name;
    private $hmac_create_etm;
    private $hmac_check = \false;
    private $hmac_check_name;
    private $hmac_check_etm;
    private $hmac_size = \false;
    private $server_public_host_key;
    private $session_id = \false;
    private $exchange_hash = \false;
    private static $message_numbers = [];
    private static $disconnect_reasons = [];
    private static $channel_open_failure_reasons = [];
    private static $terminal_modes = [];
    private static $channel_extended_data_type_codes = [];
    private $send_seq_no = 0;
    private $get_seq_no = 0;
    protected $server_channels = [];
    private $channel_buffers = [];
    protected $channel_status = [];
    private $channel_id_last_interactive = 0;
    private $packet_size_client_to_server = [];
    private $message_number_log = [];
    private $message_log = [];
    protected $window_size = 0x7fffffff;
    private $window_resize = 0x40000000;
    protected $window_size_server_to_client = [];
    private $window_size_client_to_server = [];
    private $signature = '';
    private $signature_format = '';
    private $interactiveBuffer = '';
    private $log_size;
    protected $timeout;
    protected $curTimeout;
    private $keepAlive;
    private $realtime_log_file;
    private $realtime_log_size;
    private $signature_validated = \false;
    private $realtime_log_wrap;
    private $quiet_mode = \false;
    private $last_packet;
    private $exit_status;
    private $request_pty = \false;
    private $stdErrorLog;
    private $last_interactive_response = '';
    private $keyboard_requests_responses = [];
    private $banner_message = '';
    private $is_timeout = \false;
    private $log_boundary = ':';
    private $log_long_width = 65;
    private $log_short_width = 16;
    private $host;
    private $port;
    private $windowColumns = 80;
    private $windowRows = 24;
    private static $crypto_engine = \false;
    private $agent;
    private static $connections;
    private $send_id_string_first = \true;
    private $send_kex_first = \true;
    private $bad_key_size_fix = \false;
    private $login_credentials_finalized = \false;
    private $binary_packet_buffer = null;
    protected $preferred_signature_format = \false;
    protected $auth = [];
    private $term = 'vt100';
    private $auth_methods_to_continue = null;
    private $compress = self::NET_SSH2_COMPRESSION_NONE;
    private $decompress = self::NET_SSH2_COMPRESSION_NONE;
    private $compress_context;
    private $decompress_context;
    private $regenerate_compression_context = \false;
    private $regenerate_decompression_context = \false;
    private $smartMFA = \true;
    private $channelCount = 0;
    private $errorOnMultipleChannels;
    private $extra_packets;
    public function __construct($host, $port = 22, $timeout = 10)
    {
        if (empty(self::$message_numbers)) {
            self::$message_numbers = [1 => 'Staatic\Vendor\NET_SSH2_MSG_DISCONNECT', 2 => 'Staatic\Vendor\NET_SSH2_MSG_IGNORE', 3 => 'Staatic\Vendor\NET_SSH2_MSG_UNIMPLEMENTED', 4 => 'Staatic\Vendor\NET_SSH2_MSG_DEBUG', 5 => 'Staatic\Vendor\NET_SSH2_MSG_SERVICE_REQUEST', 6 => 'Staatic\Vendor\NET_SSH2_MSG_SERVICE_ACCEPT', 7 => 'Staatic\Vendor\NET_SSH2_MSG_EXT_INFO', 20 => 'Staatic\Vendor\NET_SSH2_MSG_KEXINIT', 21 => 'Staatic\Vendor\NET_SSH2_MSG_NEWKEYS', 30 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_INIT', 31 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_REPLY', 50 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_REQUEST', 51 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_FAILURE', 52 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_SUCCESS', 53 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_BANNER', 80 => 'Staatic\Vendor\NET_SSH2_MSG_GLOBAL_REQUEST', 81 => 'Staatic\Vendor\NET_SSH2_MSG_REQUEST_SUCCESS', 82 => 'Staatic\Vendor\NET_SSH2_MSG_REQUEST_FAILURE', 90 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN', 91 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN_CONFIRMATION', 92 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_OPEN_FAILURE', 93 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_WINDOW_ADJUST', 94 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_DATA', 95 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_EXTENDED_DATA', 96 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_EOF', 97 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_CLOSE', 98 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_REQUEST', 99 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_SUCCESS', 100 => 'Staatic\Vendor\NET_SSH2_MSG_CHANNEL_FAILURE'];
            self::$disconnect_reasons = [1 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_HOST_NOT_ALLOWED_TO_CONNECT', 2 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_PROTOCOL_ERROR', 3 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED', 4 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_RESERVED', 5 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_MAC_ERROR', 6 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_COMPRESSION_ERROR', 7 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_SERVICE_NOT_AVAILABLE', 8 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_PROTOCOL_VERSION_NOT_SUPPORTED', 9 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_HOST_KEY_NOT_VERIFIABLE', 10 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_CONNECTION_LOST', 11 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_BY_APPLICATION', 12 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_TOO_MANY_CONNECTIONS', 13 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_AUTH_CANCELLED_BY_USER', 14 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_NO_MORE_AUTH_METHODS_AVAILABLE', 15 => 'Staatic\Vendor\NET_SSH2_DISCONNECT_ILLEGAL_USER_NAME'];
            self::$channel_open_failure_reasons = [1 => 'Staatic\Vendor\NET_SSH2_OPEN_ADMINISTRATIVELY_PROHIBITED'];
            self::$terminal_modes = [0 => 'Staatic\Vendor\NET_SSH2_TTY_OP_END'];
            self::$channel_extended_data_type_codes = [1 => 'Staatic\Vendor\NET_SSH2_EXTENDED_DATA_STDERR'];
            self::define_array(self::$message_numbers, self::$disconnect_reasons, self::$channel_open_failure_reasons, self::$terminal_modes, self::$channel_extended_data_type_codes, [60 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PASSWD_CHANGEREQ'], [60 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PK_OK'], [60 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_REQUEST', 61 => 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_RESPONSE'], [30 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REQUEST_OLD', 31 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_GROUP', 32 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_INIT', 33 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REPLY', 34 => 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REQUEST'], [30 => 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_INIT', 31 => 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_REPLY']);
        }
        self::$connections[$this->getResourceId()] = class_exists('WeakReference') ? WeakReference::create($this) : $this;
        $this->timeout = $timeout;
        if (is_resource($host)) {
            $this->fsock = $host;
            return;
        }
        if (Strings::is_stringable($host)) {
            $this->host = $host;
            $this->port = $port;
        }
    }
    public static function setCryptoEngine($engine)
    {
        self::$crypto_engine = $engine;
    }
    public function sendIdentificationStringFirst()
    {
        $this->send_id_string_first = \true;
    }
    public function sendIdentificationStringLast()
    {
        $this->send_id_string_first = \false;
    }
    public function sendKEXINITFirst()
    {
        $this->send_kex_first = \true;
    }
    public function sendKEXINITLast()
    {
        $this->send_kex_first = \false;
    }
    private static function stream_select(&$read, &$write, &$except, $seconds, $microseconds = null)
    {
        $remaining = $seconds + $microseconds / 1000000;
        $start = microtime(\true);
        while (\true) {
            $result = @stream_select($read, $write, $except, $seconds, $microseconds);
            if ($result !== \false) {
                return $result;
            }
            $elapsed = microtime(\true) - $start;
            $seconds = (int) ($remaining - floor($elapsed));
            $microseconds = (int) (1000000 * ($remaining - $seconds));
            if ($elapsed >= $remaining) {
                return \false;
            }
        }
    }
    private function connect()
    {
        if ($this->bitmap & self::MASK_CONSTRUCTOR) {
            return;
        }
        $this->bitmap |= self::MASK_CONSTRUCTOR;
        $this->curTimeout = $this->timeout;
        $this->last_packet = microtime(\true);
        if (!is_resource($this->fsock)) {
            $start = microtime(\true);
            $this->fsock = @fsockopen($this->host, $this->port, $errno, $errstr, ($this->curTimeout == 0) ? 100000 : $this->curTimeout);
            if (!$this->fsock) {
                $host = $this->host . ':' . $this->port;
                throw new UnableToConnectException(rtrim("Cannot connect to {$host}. Error {$errno}. {$errstr}"));
            }
            $elapsed = microtime(\true) - $start;
            if ($this->curTimeout) {
                $this->curTimeout -= $elapsed;
                if ($this->curTimeout < 0) {
                    throw new RuntimeException('Connection timed out whilst attempting to open socket connection');
                }
            }
        }
        $this->identifier = $this->generate_identifier();
        if ($this->send_id_string_first) {
            fputs($this->fsock, $this->identifier . "\r\n");
        }
        $data = '';
        while (!feof($this->fsock) && !preg_match('#(.*)^(SSH-(\d\.\d+).*)#ms', $data, $matches)) {
            $line = '';
            while (\true) {
                if ($this->curTimeout) {
                    if ($this->curTimeout < 0) {
                        throw new RuntimeException('Connection timed out whilst receiving server identification string');
                    }
                    $read = [$this->fsock];
                    $write = $except = null;
                    $start = microtime(\true);
                    $sec = (int) floor($this->curTimeout);
                    $usec = (int) (1000000 * ($this->curTimeout - $sec));
                    if (static::stream_select($read, $write, $except, $sec, $usec) === \false) {
                        throw new RuntimeException('Connection timed out whilst receiving server identification string');
                    }
                    $elapsed = microtime(\true) - $start;
                    $this->curTimeout -= $elapsed;
                }
                $temp = stream_get_line($this->fsock, 255, "\n");
                if ($temp === \false) {
                    throw new RuntimeException('Error reading from socket');
                }
                if (strlen($temp) == 255) {
                    continue;
                }
                $line .= "{$temp}\n";
                break;
            }
            $data .= $line;
        }
        if (feof($this->fsock)) {
            $this->bitmap = 0;
            throw new ConnectionClosedException('Connection closed by server');
        }
        $extra = $matches[1];
        if (defined('Staatic\Vendor\NET_SSH2_LOGGING')) {
            $this->append_log('<-', $matches[0]);
            $this->append_log('->', $this->identifier . "\r\n");
        }
        $this->server_identifier = trim($temp, "\r\n");
        if (strlen($extra)) {
            $this->errors[] = $data;
        }
        if (version_compare($matches[3], '1.99', '<')) {
            $this->bitmap = 0;
            throw new UnableToConnectException("Cannot connect to SSH {$matches[3]} servers");
        }
        $pattern = '#^SSH-2\.0-OpenSSH_([\d.]+)[^ ]* Ubuntu-.*$#';
        $match = preg_match($pattern, $this->server_identifier, $matches);
        $match = $match && version_compare('5.8', $matches[1], '<=');
        $match = $match && version_compare('6.9', $matches[1], '>=');
        $this->errorOnMultipleChannels = $match;
        if (!$this->send_id_string_first) {
            fputs($this->fsock, $this->identifier . "\r\n");
        }
        if (!$this->send_kex_first) {
            $response = $this->get_binary_packet();
            if (is_bool($response) || !strlen($response) || ord($response[0]) != NET_SSH2_MSG_KEXINIT) {
                $this->bitmap = 0;
                throw new UnexpectedValueException('Expected SSH_MSG_KEXINIT');
            }
            $this->key_exchange($response);
        }
        if ($this->send_kex_first) {
            $this->key_exchange();
        }
        $this->bitmap |= self::MASK_CONNECTED;
        return \true;
    }
    private function generate_identifier()
    {
        $identifier = 'SSH-2.0-phpseclib_3.0';
        $ext = [];
        if (extension_loaded('sodium')) {
            $ext[] = 'libsodium';
        }
        if (extension_loaded('openssl')) {
            $ext[] = 'openssl';
        } elseif (extension_loaded('mcrypt')) {
            $ext[] = 'mcrypt';
        }
        if (extension_loaded('gmp')) {
            $ext[] = 'gmp';
        } elseif (extension_loaded('bcmath')) {
            $ext[] = 'bcmath';
        }
        if (!empty($ext)) {
            $identifier .= ' (' . implode(', ', $ext) . ')';
        }
        return $identifier;
    }
    private function key_exchange($kexinit_payload_server = \false)
    {
        $preferred = $this->preferred;
        $send_kex = \true;
        $kex_algorithms = isset($preferred['kex']) ? $preferred['kex'] : SSH2::getSupportedKEXAlgorithms();
        $server_host_key_algorithms = isset($preferred['hostkey']) ? $preferred['hostkey'] : SSH2::getSupportedHostKeyAlgorithms();
        $s2c_encryption_algorithms = isset($preferred['server_to_client']['crypt']) ? $preferred['server_to_client']['crypt'] : SSH2::getSupportedEncryptionAlgorithms();
        $c2s_encryption_algorithms = isset($preferred['client_to_server']['crypt']) ? $preferred['client_to_server']['crypt'] : SSH2::getSupportedEncryptionAlgorithms();
        $s2c_mac_algorithms = isset($preferred['server_to_client']['mac']) ? $preferred['server_to_client']['mac'] : SSH2::getSupportedMACAlgorithms();
        $c2s_mac_algorithms = isset($preferred['client_to_server']['mac']) ? $preferred['client_to_server']['mac'] : SSH2::getSupportedMACAlgorithms();
        $s2c_compression_algorithms = isset($preferred['server_to_client']['comp']) ? $preferred['server_to_client']['comp'] : SSH2::getSupportedCompressionAlgorithms();
        $c2s_compression_algorithms = isset($preferred['client_to_server']['comp']) ? $preferred['client_to_server']['comp'] : SSH2::getSupportedCompressionAlgorithms();
        $kex_algorithms = array_merge($kex_algorithms, ['ext-info-c', 'kex-strict-c-v00@openssh.com']);
        switch (\true) {
            case $this->server_identifier == 'SSH-2.0-SSHD':
            case substr($this->server_identifier, 0, 13) == 'SSH-2.0-DLINK':
                if (!isset($preferred['server_to_client']['mac'])) {
                    $s2c_mac_algorithms = array_values(array_diff($s2c_mac_algorithms, ['hmac-sha1-96', 'hmac-md5-96']));
                }
                if (!isset($preferred['client_to_server']['mac'])) {
                    $c2s_mac_algorithms = array_values(array_diff($c2s_mac_algorithms, ['hmac-sha1-96', 'hmac-md5-96']));
                }
                break;
            case substr($this->server_identifier, 0, 24) == 'SSH-2.0-TurboFTP_SERVER_':
                if (!isset($preferred['server_to_client']['crypt'])) {
                    $s2c_encryption_algorithms = array_values(array_diff($s2c_encryption_algorithms, ['aes128-gcm@openssh.com', 'aes256-gcm@openssh.com']));
                }
                if (!isset($preferred['client_to_server']['crypt'])) {
                    $c2s_encryption_algorithms = array_values(array_diff($c2s_encryption_algorithms, ['aes128-gcm@openssh.com', 'aes256-gcm@openssh.com']));
                }
        }
        $client_cookie = Random::string(16);
        $kexinit_payload_client = pack('Ca*', NET_SSH2_MSG_KEXINIT, $client_cookie);
        $kexinit_payload_client .= Strings::packSSH2('L10bN', $kex_algorithms, $server_host_key_algorithms, $c2s_encryption_algorithms, $s2c_encryption_algorithms, $c2s_mac_algorithms, $s2c_mac_algorithms, $c2s_compression_algorithms, $s2c_compression_algorithms, [], [], \false, 0);
        if ($kexinit_payload_server === \false) {
            $this->send_binary_packet($kexinit_payload_client);
            $this->extra_packets = 0;
            $kexinit_payload_server = $this->get_binary_packet();
            if (is_bool($kexinit_payload_server) || !strlen($kexinit_payload_server) || ord($kexinit_payload_server[0]) != NET_SSH2_MSG_KEXINIT) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
                throw new UnexpectedValueException('Expected SSH_MSG_KEXINIT');
            }
            $send_kex = \false;
        }
        $response = $kexinit_payload_server;
        Strings::shift($response, 1);
        $server_cookie = Strings::shift($response, 16);
        list($this->kex_algorithms, $this->server_host_key_algorithms, $this->encryption_algorithms_client_to_server, $this->encryption_algorithms_server_to_client, $this->mac_algorithms_client_to_server, $this->mac_algorithms_server_to_client, $this->compression_algorithms_client_to_server, $this->compression_algorithms_server_to_client, $this->languages_client_to_server, $this->languages_server_to_client, $first_kex_packet_follows) = Strings::unpackSSH2('L10C', $response);
        if (in_array('kex-strict-s-v00@openssh.com', $this->kex_algorithms)) {
            if ($this->session_id === \false && $this->extra_packets) {
                throw new UnexpectedValueException('Possible Terrapin Attack detected');
            }
        }
        $this->supported_private_key_algorithms = $this->server_host_key_algorithms;
        if ($send_kex) {
            $this->send_binary_packet($kexinit_payload_client);
        }
        $decrypt = self::array_intersect_first($s2c_encryption_algorithms, $this->encryption_algorithms_server_to_client);
        if (!$decrypt || ($decryptKeyLength = $this->encryption_algorithm_to_key_size($decrypt)) === null) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible server to client encryption algorithms found');
        }
        $encrypt = self::array_intersect_first($c2s_encryption_algorithms, $this->encryption_algorithms_client_to_server);
        if (!$encrypt || ($encryptKeyLength = $this->encryption_algorithm_to_key_size($encrypt)) === null) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible client to server encryption algorithms found');
        }
        $this->kex_algorithm = self::array_intersect_first($kex_algorithms, $this->kex_algorithms);
        if ($this->kex_algorithm === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible key exchange algorithms found');
        }
        $server_host_key_algorithm = self::array_intersect_first($server_host_key_algorithms, $this->server_host_key_algorithms);
        if ($server_host_key_algorithm === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible server host key algorithms found');
        }
        $mac_algorithm_out = self::array_intersect_first($c2s_mac_algorithms, $this->mac_algorithms_client_to_server);
        if ($mac_algorithm_out === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible client to server message authentication algorithms found');
        }
        $mac_algorithm_in = self::array_intersect_first($s2c_mac_algorithms, $this->mac_algorithms_server_to_client);
        if ($mac_algorithm_in === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible server to client message authentication algorithms found');
        }
        $compression_map = ['none' => self::NET_SSH2_COMPRESSION_NONE, 'zlib' => self::NET_SSH2_COMPRESSION_ZLIB, 'zlib@openssh.com' => self::NET_SSH2_COMPRESSION_ZLIB_AT_OPENSSH];
        $compression_algorithm_in = self::array_intersect_first($s2c_compression_algorithms, $this->compression_algorithms_server_to_client);
        if ($compression_algorithm_in === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible server to client compression algorithms found');
        }
        $this->decompress = $compression_map[$compression_algorithm_in];
        $compression_algorithm_out = self::array_intersect_first($c2s_compression_algorithms, $this->compression_algorithms_client_to_server);
        if ($compression_algorithm_out === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_KEY_EXCHANGE_FAILED);
            throw new NoSupportedAlgorithmsException('No compatible client to server compression algorithms found');
        }
        $this->compress = $compression_map[$compression_algorithm_out];
        switch ($this->kex_algorithm) {
            case 'diffie-hellman-group15-sha512':
            case 'diffie-hellman-group16-sha512':
            case 'diffie-hellman-group17-sha512':
            case 'diffie-hellman-group18-sha512':
            case 'ecdh-sha2-nistp521':
                $kexHash = new Hash('sha512');
                break;
            case 'ecdh-sha2-nistp384':
                $kexHash = new Hash('sha384');
                break;
            case 'diffie-hellman-group-exchange-sha256':
            case 'diffie-hellman-group14-sha256':
            case 'ecdh-sha2-nistp256':
            case 'curve25519-sha256@libssh.org':
            case 'curve25519-sha256':
                $kexHash = new Hash('sha256');
                break;
            default:
                $kexHash = new Hash('sha1');
        }
        $exchange_hash_rfc4419 = '';
        if (strpos($this->kex_algorithm, 'curve25519-sha256') === 0 || strpos($this->kex_algorithm, 'ecdh-sha2-nistp') === 0) {
            $curve = (strpos($this->kex_algorithm, 'curve25519-sha256') === 0) ? 'Curve25519' : substr($this->kex_algorithm, 10);
            $ourPrivate = EC::createKey($curve);
            $ourPublicBytes = $ourPrivate->getPublicKey()->getEncodedCoordinates();
            $clientKexInitMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_INIT';
            $serverKexReplyMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_REPLY';
        } else {
            if (strpos($this->kex_algorithm, 'diffie-hellman-group-exchange') === 0) {
                $dh_group_sizes_packed = pack('NNN', $this->kex_dh_group_size_min, $this->kex_dh_group_size_preferred, $this->kex_dh_group_size_max);
                $packet = pack('Ca*', NET_SSH2_MSG_KEXDH_GEX_REQUEST, $dh_group_sizes_packed);
                $this->send_binary_packet($packet);
                $this->updateLogHistory('UNKNOWN (34)', 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REQUEST');
                $response = $this->get_binary_packet();
                list($type, $primeBytes, $gBytes) = Strings::unpackSSH2('Css', $response);
                if ($type != NET_SSH2_MSG_KEXDH_GEX_GROUP) {
                    $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
                    throw new UnexpectedValueException('Expected SSH_MSG_KEX_DH_GEX_GROUP');
                }
                $this->updateLogHistory('Staatic\Vendor\NET_SSH2_MSG_KEXDH_REPLY', 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_GROUP');
                $prime = new BigInteger($primeBytes, -256);
                $g = new BigInteger($gBytes, -256);
                $exchange_hash_rfc4419 = $dh_group_sizes_packed . Strings::packSSH2('ss', $primeBytes, $gBytes);
                $params = DH::createParameters($prime, $g);
                $clientKexInitMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_INIT';
                $serverKexReplyMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REPLY';
            } else {
                $params = DH::createParameters($this->kex_algorithm);
                $clientKexInitMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_INIT';
                $serverKexReplyMessage = 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_REPLY';
            }
            $keyLength = min($kexHash->getLengthInBytes(), max($encryptKeyLength, $decryptKeyLength));
            $ourPrivate = DH::createKey($params, 16 * $keyLength);
            $ourPublic = $ourPrivate->getPublicKey()->toBigInteger();
            $ourPublicBytes = $ourPublic->toBytes(\true);
        }
        $data = pack('CNa*', constant($clientKexInitMessage), strlen($ourPublicBytes), $ourPublicBytes);
        $this->send_binary_packet($data);
        switch ($clientKexInitMessage) {
            case 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_INIT':
                $this->updateLogHistory('Staatic\Vendor\NET_SSH2_MSG_KEXDH_INIT', 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_INIT');
                break;
            case 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_INIT':
                $this->updateLogHistory('UNKNOWN (32)', 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_INIT');
        }
        $response = $this->get_binary_packet();
        list($type, $server_public_host_key, $theirPublicBytes, $this->signature) = Strings::unpackSSH2('Csss', $response);
        if ($type != constant($serverKexReplyMessage)) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
            throw new UnexpectedValueException("Expected {$serverKexReplyMessage}");
        }
        switch ($serverKexReplyMessage) {
            case 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_REPLY':
                $this->updateLogHistory('Staatic\Vendor\NET_SSH2_MSG_KEXDH_REPLY', 'Staatic\Vendor\NET_SSH2_MSG_KEX_ECDH_REPLY');
                break;
            case 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REPLY':
                $this->updateLogHistory('UNKNOWN (33)', 'Staatic\Vendor\NET_SSH2_MSG_KEXDH_GEX_REPLY');
        }
        $this->server_public_host_key = $server_public_host_key;
        list($public_key_format) = Strings::unpackSSH2('s', $server_public_host_key);
        if (strlen($this->signature) < 4) {
            throw new LengthException('The signature needs at least four bytes');
        }
        $temp = unpack('Nlength', substr($this->signature, 0, 4));
        $this->signature_format = substr($this->signature, 4, $temp['length']);
        $keyBytes = DH::computeSecret($ourPrivate, $theirPublicBytes);
        if (($keyBytes & "\xff\x80") === "\x00\x00") {
            $keyBytes = substr($keyBytes, 1);
        } elseif (($keyBytes[0] & "\x80") === "\x80") {
            $keyBytes = "\x00{$keyBytes}";
        }
        $this->exchange_hash = Strings::packSSH2('s5', $this->identifier, $this->server_identifier, $kexinit_payload_client, $kexinit_payload_server, $this->server_public_host_key);
        $this->exchange_hash .= $exchange_hash_rfc4419;
        $this->exchange_hash .= Strings::packSSH2('s3', $ourPublicBytes, $theirPublicBytes, $keyBytes);
        $this->exchange_hash = $kexHash->hash($this->exchange_hash);
        if ($this->session_id === \false) {
            $this->session_id = $this->exchange_hash;
        }
        switch ($server_host_key_algorithm) {
            case 'rsa-sha2-256':
            case 'rsa-sha2-512':
                $expected_key_format = 'ssh-rsa';
                break;
            default:
                $expected_key_format = $server_host_key_algorithm;
        }
        if ($public_key_format != $expected_key_format || $this->signature_format != $server_host_key_algorithm) {
            switch (\true) {
                case $this->signature_format == $server_host_key_algorithm:
                case $server_host_key_algorithm != 'rsa-sha2-256' && $server_host_key_algorithm != 'rsa-sha2-512':
                case $this->signature_format != 'ssh-rsa':
                    $this->disconnect_helper(NET_SSH2_DISCONNECT_HOST_KEY_NOT_VERIFIABLE);
                    throw new RuntimeException('Server Host Key Algorithm Mismatch (' . $this->signature_format . ' vs ' . $server_host_key_algorithm . ')');
            }
        }
        $packet = pack('C', NET_SSH2_MSG_NEWKEYS);
        $this->send_binary_packet($packet);
        $response = $this->get_binary_packet();
        if ($response === \false) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_CONNECTION_LOST);
            throw new ConnectionClosedException('Connection closed by server');
        }
        list($type) = Strings::unpackSSH2('C', $response);
        if ($type != NET_SSH2_MSG_NEWKEYS) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
            throw new UnexpectedValueException('Expected SSH_MSG_NEWKEYS');
        }
        if (in_array('kex-strict-s-v00@openssh.com', $this->kex_algorithms)) {
            $this->get_seq_no = $this->send_seq_no = 0;
        }
        $keyBytes = pack('Na*', strlen($keyBytes), $keyBytes);
        $this->encrypt = self::encryption_algorithm_to_crypt_instance($encrypt);
        if ($this->encrypt) {
            if (self::$crypto_engine) {
                $this->encrypt->setPreferredEngine(self::$crypto_engine);
            }
            if ($this->encrypt->getBlockLengthInBytes()) {
                $this->encrypt_block_size = $this->encrypt->getBlockLengthInBytes();
            }
            $this->encrypt->disablePadding();
            if ($this->encrypt->usesIV()) {
                $iv = $kexHash->hash($keyBytes . $this->exchange_hash . 'A' . $this->session_id);
                while ($this->encrypt_block_size > strlen($iv)) {
                    $iv .= $kexHash->hash($keyBytes . $this->exchange_hash . $iv);
                }
                $this->encrypt->setIV(substr($iv, 0, $this->encrypt_block_size));
            }
            switch ($encrypt) {
                case 'aes128-gcm@openssh.com':
                case 'aes256-gcm@openssh.com':
                    $nonce = $kexHash->hash($keyBytes . $this->exchange_hash . 'A' . $this->session_id);
                    $this->encryptFixedPart = substr($nonce, 0, 4);
                    $this->encryptInvocationCounter = substr($nonce, 4, 8);
                case 'chacha20-poly1305@openssh.com':
                    break;
                default:
                    $this->encrypt->enableContinuousBuffer();
            }
            $key = $kexHash->hash($keyBytes . $this->exchange_hash . 'C' . $this->session_id);
            while ($encryptKeyLength > strlen($key)) {
                $key .= $kexHash->hash($keyBytes . $this->exchange_hash . $key);
            }
            switch ($encrypt) {
                case 'chacha20-poly1305@openssh.com':
                    $encryptKeyLength = 32;
                    $this->lengthEncrypt = self::encryption_algorithm_to_crypt_instance($encrypt);
                    $this->lengthEncrypt->setKey(substr($key, 32, 32));
            }
            $this->encrypt->setKey(substr($key, 0, $encryptKeyLength));
            $this->encryptName = $encrypt;
        }
        $this->decrypt = self::encryption_algorithm_to_crypt_instance($decrypt);
        if ($this->decrypt) {
            if (self::$crypto_engine) {
                $this->decrypt->setPreferredEngine(self::$crypto_engine);
            }
            if ($this->decrypt->getBlockLengthInBytes()) {
                $this->decrypt_block_size = $this->decrypt->getBlockLengthInBytes();
            }
            $this->decrypt->disablePadding();
            if ($this->decrypt->usesIV()) {
                $iv = $kexHash->hash($keyBytes . $this->exchange_hash . 'B' . $this->session_id);
                while ($this->decrypt_block_size > strlen($iv)) {
                    $iv .= $kexHash->hash($keyBytes . $this->exchange_hash . $iv);
                }
                $this->decrypt->setIV(substr($iv, 0, $this->decrypt_block_size));
            }
            switch ($decrypt) {
                case 'aes128-gcm@openssh.com':
                case 'aes256-gcm@openssh.com':
                    $nonce = $kexHash->hash($keyBytes . $this->exchange_hash . 'B' . $this->session_id);
                    $this->decryptFixedPart = substr($nonce, 0, 4);
                    $this->decryptInvocationCounter = substr($nonce, 4, 8);
                case 'chacha20-poly1305@openssh.com':
                    break;
                default:
                    $this->decrypt->enableContinuousBuffer();
            }
            $key = $kexHash->hash($keyBytes . $this->exchange_hash . 'D' . $this->session_id);
            while ($decryptKeyLength > strlen($key)) {
                $key .= $kexHash->hash($keyBytes . $this->exchange_hash . $key);
            }
            switch ($decrypt) {
                case 'chacha20-poly1305@openssh.com':
                    $decryptKeyLength = 32;
                    $this->lengthDecrypt = self::encryption_algorithm_to_crypt_instance($decrypt);
                    $this->lengthDecrypt->setKey(substr($key, 32, 32));
            }
            $this->decrypt->setKey(substr($key, 0, $decryptKeyLength));
            $this->decryptName = $decrypt;
        }
        if ($encrypt == 'arcfour128' || $encrypt == 'arcfour256') {
            $this->encrypt->encrypt(str_repeat("\x00", 1536));
        }
        if ($decrypt == 'arcfour128' || $decrypt == 'arcfour256') {
            $this->decrypt->decrypt(str_repeat("\x00", 1536));
        }
        if (!$this->encrypt->usesNonce()) {
            list($this->hmac_create, $createKeyLength) = self::mac_algorithm_to_hash_instance($mac_algorithm_out);
        } else {
            $this->hmac_create = new stdClass();
            $this->hmac_create_name = $mac_algorithm_out;
            $createKeyLength = 0;
        }
        if ($this->hmac_create instanceof Hash) {
            $key = $kexHash->hash($keyBytes . $this->exchange_hash . 'E' . $this->session_id);
            while ($createKeyLength > strlen($key)) {
                $key .= $kexHash->hash($keyBytes . $this->exchange_hash . $key);
            }
            $this->hmac_create->setKey(substr($key, 0, $createKeyLength));
            $this->hmac_create_name = $mac_algorithm_out;
            $this->hmac_create_etm = preg_match('#-etm@openssh\.com$#', $mac_algorithm_out);
        }
        if (!$this->decrypt->usesNonce()) {
            list($this->hmac_check, $checkKeyLength) = self::mac_algorithm_to_hash_instance($mac_algorithm_in);
            $this->hmac_size = $this->hmac_check->getLengthInBytes();
        } else {
            $this->hmac_check = new stdClass();
            $this->hmac_check_name = $mac_algorithm_in;
            $checkKeyLength = 0;
            $this->hmac_size = 0;
        }
        if ($this->hmac_check instanceof Hash) {
            $key = $kexHash->hash($keyBytes . $this->exchange_hash . 'F' . $this->session_id);
            while ($checkKeyLength > strlen($key)) {
                $key .= $kexHash->hash($keyBytes . $this->exchange_hash . $key);
            }
            $this->hmac_check->setKey(substr($key, 0, $checkKeyLength));
            $this->hmac_check_name = $mac_algorithm_in;
            $this->hmac_check_etm = preg_match('#-etm@openssh\.com$#', $mac_algorithm_in);
        }
        $this->regenerate_compression_context = $this->regenerate_decompression_context = \true;
        return \true;
    }
    private function encryption_algorithm_to_key_size($algorithm)
    {
        if ($this->bad_key_size_fix && self::bad_algorithm_candidate($algorithm)) {
            return 16;
        }
        switch ($algorithm) {
            case 'none':
                return 0;
            case 'aes128-gcm@openssh.com':
            case 'aes128-cbc':
            case 'aes128-ctr':
            case 'arcfour':
            case 'arcfour128':
            case 'blowfish-cbc':
            case 'blowfish-ctr':
            case 'twofish128-cbc':
            case 'twofish128-ctr':
                return 16;
            case '3des-cbc':
            case '3des-ctr':
            case 'aes192-cbc':
            case 'aes192-ctr':
            case 'twofish192-cbc':
            case 'twofish192-ctr':
                return 24;
            case 'aes256-gcm@openssh.com':
            case 'aes256-cbc':
            case 'aes256-ctr':
            case 'arcfour256':
            case 'twofish-cbc':
            case 'twofish256-cbc':
            case 'twofish256-ctr':
                return 32;
            case 'chacha20-poly1305@openssh.com':
                return 64;
        }
        return null;
    }
    private static function encryption_algorithm_to_crypt_instance($algorithm)
    {
        switch ($algorithm) {
            case '3des-cbc':
                return new TripleDES('cbc');
            case '3des-ctr':
                return new TripleDES('ctr');
            case 'aes256-cbc':
            case 'aes192-cbc':
            case 'aes128-cbc':
                return new Rijndael('cbc');
            case 'aes256-ctr':
            case 'aes192-ctr':
            case 'aes128-ctr':
                return new Rijndael('ctr');
            case 'blowfish-cbc':
                return new Blowfish('cbc');
            case 'blowfish-ctr':
                return new Blowfish('ctr');
            case 'twofish128-cbc':
            case 'twofish192-cbc':
            case 'twofish256-cbc':
            case 'twofish-cbc':
                return new Twofish('cbc');
            case 'twofish128-ctr':
            case 'twofish192-ctr':
            case 'twofish256-ctr':
                return new Twofish('ctr');
            case 'arcfour':
            case 'arcfour128':
            case 'arcfour256':
                return new RC4();
            case 'aes128-gcm@openssh.com':
            case 'aes256-gcm@openssh.com':
                return new Rijndael('gcm');
            case 'chacha20-poly1305@openssh.com':
                return new ChaCha20();
        }
        return null;
    }
    private static function mac_algorithm_to_hash_instance($algorithm)
    {
        switch ($algorithm) {
            case 'umac-64@openssh.com':
            case 'umac-64-etm@openssh.com':
                return [new Hash('umac-64'), 16];
            case 'umac-128@openssh.com':
            case 'umac-128-etm@openssh.com':
                return [new Hash('umac-128'), 16];
            case 'hmac-sha2-512':
            case 'hmac-sha2-512-etm@openssh.com':
                return [new Hash('sha512'), 64];
            case 'hmac-sha2-256':
            case 'hmac-sha2-256-etm@openssh.com':
                return [new Hash('sha256'), 32];
            case 'hmac-sha1':
            case 'hmac-sha1-etm@openssh.com':
                return [new Hash('sha1'), 20];
            case 'hmac-sha1-96':
                return [new Hash('sha1-96'), 20];
            case 'hmac-md5':
                return [new Hash('md5'), 16];
            case 'hmac-md5-96':
                return [new Hash('md5-96'), 16];
        }
    }
    private static function bad_algorithm_candidate($algorithm)
    {
        switch ($algorithm) {
            case 'arcfour256':
            case 'aes192-ctr':
            case 'aes256-ctr':
                return \true;
        }
        return \false;
    }
    public function login($username, ...$args)
    {
        if (!$this->login_credentials_finalized) {
            $this->auth[] = func_get_args();
        }
        if (substr($this->server_identifier, 0, 15) != 'SSH-2.0-CoreFTP' && $this->auth_methods_to_continue === null) {
            if ($this->sublogin($username)) {
                return \true;
            }
            if (!count($args)) {
                return \false;
            }
        }
        return $this->sublogin($username, ...$args);
    }
    protected function sublogin($username, ...$args)
    {
        if (!($this->bitmap & self::MASK_CONSTRUCTOR)) {
            $this->connect();
        }
        if (empty($args)) {
            return $this->login_helper($username);
        }
        foreach ($args as $arg) {
            switch (\true) {
                case $arg instanceof PublicKey:
                    throw new UnexpectedValueException('A PublicKey object was passed to the login method instead of a PrivateKey object');
                case $arg instanceof PrivateKey:
                case $arg instanceof Agent:
                case is_array($arg):
                case Strings::is_stringable($arg):
                    break;
                default:
                    throw new UnexpectedValueException('$password needs to either be an instance of \phpseclib3\Crypt\Common\PrivateKey, \System\SSH\Agent, an array or a string');
            }
        }
        while (count($args)) {
            if (!$this->auth_methods_to_continue || !$this->smartMFA) {
                $newargs = $args;
                $args = [];
            } else {
                $newargs = [];
                foreach ($this->auth_methods_to_continue as $method) {
                    switch ($method) {
                        case 'publickey':
                            foreach ($args as $key => $arg) {
                                if ($arg instanceof PrivateKey || $arg instanceof Agent) {
                                    $newargs[] = $arg;
                                    unset($args[$key]);
                                    break;
                                }
                            }
                            break;
                        case 'keyboard-interactive':
                            $hasArray = $hasString = \false;
                            foreach ($args as $arg) {
                                if ($hasArray || is_array($arg)) {
                                    $hasArray = \true;
                                    break;
                                }
                                if ($hasString || Strings::is_stringable($arg)) {
                                    $hasString = \true;
                                    break;
                                }
                            }
                            if ($hasArray && $hasString) {
                                foreach ($args as $key => $arg) {
                                    if (is_array($arg)) {
                                        $newargs[] = $arg;
                                        break 2;
                                    }
                                }
                            }
                        case 'password':
                            foreach ($args as $key => $arg) {
                                $newargs[] = $arg;
                                unset($args[$key]);
                                break;
                            }
                    }
                }
            }
            if (!count($newargs)) {
                return \false;
            }
            foreach ($newargs as $arg) {
                if ($this->login_helper($username, $arg)) {
                    $this->login_credentials_finalized = \true;
                    return \true;
                }
            }
        }
        return \false;
    }
    private function login_helper($username, $password = null)
    {
        if (!($this->bitmap & self::MASK_CONNECTED)) {
            return \false;
        }
        if (!($this->bitmap & self::MASK_LOGIN_REQ)) {
            $packet = Strings::packSSH2('Cs', NET_SSH2_MSG_SERVICE_REQUEST, 'ssh-userauth');
            $this->send_binary_packet($packet);
            try {
                $response = $this->get_binary_packet();
            } catch (InvalidPacketLengthException $e) {
                if (!$this->bad_key_size_fix && $this->decryptName != null && self::bad_algorithm_candidate($this->decryptName)) {
                    $this->bad_key_size_fix = \true;
                    return $this->reconnect();
                }
                throw $e;
            } catch (Exception $e) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_CONNECTION_LOST);
                throw $e;
            }
            list($type) = Strings::unpackSSH2('C', $response);
            list($service) = Strings::unpackSSH2('s', $response);
            if ($type != NET_SSH2_MSG_SERVICE_ACCEPT || $service != 'ssh-userauth') {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
                throw new UnexpectedValueException('Expected SSH_MSG_SERVICE_ACCEPT');
            }
            $this->bitmap |= self::MASK_LOGIN_REQ;
        }
        if (strlen($this->last_interactive_response)) {
            return (!Strings::is_stringable($password) && !is_array($password)) ? \false : $this->keyboard_interactive_process($password);
        }
        if ($password instanceof PrivateKey) {
            return $this->privatekey_login($username, $password);
        }
        if ($password instanceof Agent) {
            return $this->ssh_agent_login($username, $password);
        }
        if (is_array($password)) {
            if ($this->keyboard_interactive_login($username, $password)) {
                $this->bitmap |= self::MASK_LOGIN;
                return \true;
            }
            return \false;
        }
        if (!isset($password)) {
            $packet = Strings::packSSH2('Cs3', NET_SSH2_MSG_USERAUTH_REQUEST, $username, 'ssh-connection', 'none');
            $this->send_binary_packet($packet);
            $response = $this->get_binary_packet();
            list($type) = Strings::unpackSSH2('C', $response);
            switch ($type) {
                case NET_SSH2_MSG_USERAUTH_SUCCESS:
                    $this->bitmap |= self::MASK_LOGIN;
                    return \true;
                case NET_SSH2_MSG_USERAUTH_FAILURE:
                    list($auth_methods) = Strings::unpackSSH2('L', $response);
                    $this->auth_methods_to_continue = $auth_methods;
                default:
                    return \false;
            }
        }
        $packet = Strings::packSSH2('Cs3bs', NET_SSH2_MSG_USERAUTH_REQUEST, $username, 'ssh-connection', 'password', \false, $password);
        if (!defined('Staatic\Vendor\NET_SSH2_LOGGING')) {
            $logged = null;
        } else {
            $logged = Strings::packSSH2('Cs3bs', NET_SSH2_MSG_USERAUTH_REQUEST, $username, 'ssh-connection', 'password', \false, 'password');
        }
        $this->send_binary_packet($packet, $logged);
        $response = $this->get_binary_packet();
        if ($response === \false) {
            return \false;
        }
        list($type) = Strings::unpackSSH2('C', $response);
        switch ($type) {
            case NET_SSH2_MSG_USERAUTH_PASSWD_CHANGEREQ:
                $this->updateLogHistory('UNKNOWN (60)', 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PASSWD_CHANGEREQ');
                list($message) = Strings::unpackSSH2('s', $response);
                $this->errors[] = 'SSH_MSG_USERAUTH_PASSWD_CHANGEREQ: ' . $message;
                return $this->disconnect_helper(NET_SSH2_DISCONNECT_AUTH_CANCELLED_BY_USER);
            case NET_SSH2_MSG_USERAUTH_FAILURE:
                list($auth_methods, $partial_success) = Strings::unpackSSH2('Lb', $response);
                $this->auth_methods_to_continue = $auth_methods;
                if (!$partial_success && in_array('keyboard-interactive', $auth_methods)) {
                    if ($this->keyboard_interactive_login($username, $password)) {
                        $this->bitmap |= self::MASK_LOGIN;
                        return \true;
                    }
                    return \false;
                }
                return \false;
            case NET_SSH2_MSG_USERAUTH_SUCCESS:
                $this->bitmap |= self::MASK_LOGIN;
                return \true;
        }
        return \false;
    }
    private function keyboard_interactive_login($username, $password)
    {
        $packet = Strings::packSSH2('Cs5', NET_SSH2_MSG_USERAUTH_REQUEST, $username, 'ssh-connection', 'keyboard-interactive', '', '');
        $this->send_binary_packet($packet);
        return $this->keyboard_interactive_process($password);
    }
    private function keyboard_interactive_process(...$responses)
    {
        if (strlen($this->last_interactive_response)) {
            $response = $this->last_interactive_response;
        } else {
            $orig = $response = $this->get_binary_packet();
        }
        list($type) = Strings::unpackSSH2('C', $response);
        switch ($type) {
            case NET_SSH2_MSG_USERAUTH_INFO_REQUEST:
                list(, , , $num_prompts) = Strings::unpackSSH2('s3N', $response);
                for ($i = 0; $i < count($responses); $i++) {
                    if (is_array($responses[$i])) {
                        foreach ($responses[$i] as $key => $value) {
                            $this->keyboard_requests_responses[$key] = $value;
                        }
                        unset($responses[$i]);
                    }
                }
                $responses = array_values($responses);
                if (isset($this->keyboard_requests_responses)) {
                    for ($i = 0; $i < $num_prompts; $i++) {
                        list($prompt, ) = Strings::unpackSSH2('sC', $response);
                        foreach ($this->keyboard_requests_responses as $key => $value) {
                            if (substr($prompt, 0, strlen($key)) == $key) {
                                $responses[] = $value;
                                break;
                            }
                        }
                    }
                }
                if (strlen($this->last_interactive_response)) {
                    $this->last_interactive_response = '';
                } else {
                    $this->updateLogHistory('UNKNOWN (60)', 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_REQUEST');
                }
                if (!count($responses) && $num_prompts) {
                    $this->last_interactive_response = $orig;
                    return \false;
                }
                $packet = $logged = pack('CN', NET_SSH2_MSG_USERAUTH_INFO_RESPONSE, count($responses));
                for ($i = 0; $i < count($responses); $i++) {
                    $packet .= Strings::packSSH2('s', $responses[$i]);
                    $logged .= Strings::packSSH2('s', 'dummy-answer');
                }
                $this->send_binary_packet($packet, $logged);
                $this->updateLogHistory('UNKNOWN (61)', 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_INFO_RESPONSE');
                return $this->keyboard_interactive_process();
            case NET_SSH2_MSG_USERAUTH_SUCCESS:
                return \true;
            case NET_SSH2_MSG_USERAUTH_FAILURE:
                list($auth_methods) = Strings::unpackSSH2('L', $response);
                $this->auth_methods_to_continue = $auth_methods;
                return \false;
        }
        return \false;
    }
    private function ssh_agent_login($username, Agent $agent)
    {
        $this->agent = $agent;
        $keys = $agent->requestIdentities();
        $orig_algorithms = $this->supported_private_key_algorithms;
        foreach ($keys as $key) {
            if ($this->privatekey_login($username, $key)) {
                return \true;
            }
            $this->supported_private_key_algorithms = $orig_algorithms;
        }
        return \false;
    }
    private function privatekey_login($username, PrivateKey $privatekey)
    {
        $publickey = $privatekey->getPublicKey();
        if ($publickey instanceof RSA) {
            $privatekey = $privatekey->withPadding(RSA::SIGNATURE_PKCS1);
            $algos = ['rsa-sha2-256', 'rsa-sha2-512', 'ssh-rsa'];
            if (isset($this->preferred['hostkey'])) {
                $algos = array_intersect($algos, $this->preferred['hostkey']);
            }
            $algo = self::array_intersect_first($algos, $this->supported_private_key_algorithms);
            switch ($algo) {
                case 'rsa-sha2-512':
                    $hash = 'sha512';
                    $signatureType = 'rsa-sha2-512';
                    break;
                case 'rsa-sha2-256':
                    $hash = 'sha256';
                    $signatureType = 'rsa-sha2-256';
                    break;
                default:
                    $hash = 'sha1';
                    $signatureType = 'ssh-rsa';
            }
        } elseif ($publickey instanceof EC) {
            $privatekey = $privatekey->withSignatureFormat('SSH2');
            $curveName = $privatekey->getCurve();
            switch ($curveName) {
                case 'Ed25519':
                    $hash = 'sha512';
                    $signatureType = 'ssh-ed25519';
                    break;
                case 'secp256r1':
                    $hash = 'sha256';
                    $signatureType = 'ecdsa-sha2-nistp256';
                    break;
                case 'secp384r1':
                    $hash = 'sha384';
                    $signatureType = 'ecdsa-sha2-nistp384';
                    break;
                case 'secp521r1':
                    $hash = 'sha512';
                    $signatureType = 'ecdsa-sha2-nistp521';
                    break;
                default:
                    if (is_array($curveName)) {
                        throw new UnsupportedCurveException('Specified Curves are not supported by SSH2');
                    }
                    throw new UnsupportedCurveException('Named Curve of ' . $curveName . ' is not supported by phpseclib3\'s SSH2 implementation');
            }
        } elseif ($publickey instanceof DSA) {
            $privatekey = $privatekey->withSignatureFormat('SSH2');
            $hash = 'sha1';
            $signatureType = 'ssh-dss';
        } else {
            throw new UnsupportedAlgorithmException('Please use either an RSA key, an EC one or a DSA key');
        }
        $publickeyStr = $publickey->toString('OpenSSH', ['binary' => \true]);
        $part1 = Strings::packSSH2('Csss', NET_SSH2_MSG_USERAUTH_REQUEST, $username, 'ssh-connection', 'publickey');
        $part2 = Strings::packSSH2('ss', $signatureType, $publickeyStr);
        $packet = $part1 . chr(0) . $part2;
        $this->send_binary_packet($packet);
        $response = $this->get_binary_packet();
        list($type) = Strings::unpackSSH2('C', $response);
        switch ($type) {
            case NET_SSH2_MSG_USERAUTH_FAILURE:
                list($auth_methods) = Strings::unpackSSH2('L', $response);
                if (in_array('publickey', $auth_methods) && substr($signatureType, 0, 9) == 'rsa-sha2-') {
                    $this->supported_private_key_algorithms = array_diff($this->supported_private_key_algorithms, ['rsa-sha2-256', 'rsa-sha2-512']);
                    return $this->privatekey_login($username, $privatekey);
                }
                $this->auth_methods_to_continue = $auth_methods;
                $this->errors[] = 'SSH_MSG_USERAUTH_FAILURE';
                return \false;
            case NET_SSH2_MSG_USERAUTH_PK_OK:
                $this->updateLogHistory('UNKNOWN (60)', 'Staatic\Vendor\NET_SSH2_MSG_USERAUTH_PK_OK');
                break;
            case NET_SSH2_MSG_USERAUTH_SUCCESS:
                $this->bitmap |= self::MASK_LOGIN;
                return \true;
            default:
                $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                throw new ConnectionClosedException('Unexpected response to publickey authentication pt 1');
        }
        $packet = $part1 . chr(1) . $part2;
        $privatekey = $privatekey->withHash($hash);
        $signature = $privatekey->sign(Strings::packSSH2('s', $this->session_id) . $packet);
        if ($publickey instanceof RSA) {
            $signature = Strings::packSSH2('ss', $signatureType, $signature);
        }
        $packet .= Strings::packSSH2('s', $signature);
        $this->send_binary_packet($packet);
        $response = $this->get_binary_packet();
        list($type) = Strings::unpackSSH2('C', $response);
        switch ($type) {
            case NET_SSH2_MSG_USERAUTH_FAILURE:
                list($auth_methods) = Strings::unpackSSH2('L', $response);
                $this->auth_methods_to_continue = $auth_methods;
                return \false;
            case NET_SSH2_MSG_USERAUTH_SUCCESS:
                $this->bitmap |= self::MASK_LOGIN;
                return \true;
        }
        $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
        throw new ConnectionClosedException('Unexpected response to publickey authentication pt 2');
    }
    public function getTimeout()
    {
        return $this->timeout;
    }
    public function setTimeout($timeout)
    {
        $this->timeout = $this->curTimeout = $timeout;
    }
    public function setKeepAlive($interval)
    {
        $this->keepAlive = $interval;
    }
    public function getStdError()
    {
        return $this->stdErrorLog;
    }
    /**
     * @param callable|null $callback
     */
    public function exec($command, $callback = null)
    {
        $this->curTimeout = $this->timeout;
        $this->is_timeout = \false;
        $this->stdErrorLog = '';
        if (!$this->isAuthenticated()) {
            return \false;
        }
        $this->open_channel(self::CHANNEL_EXEC);
        if ($this->request_pty === \true) {
            $terminal_modes = pack('C', NET_SSH2_TTY_OP_END);
            $packet = Strings::packSSH2('CNsCsN4s', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL_EXEC], 'pty-req', 1, $this->term, $this->windowColumns, $this->windowRows, 0, 0, $terminal_modes);
            $this->send_binary_packet($packet);
            $this->channel_status[self::CHANNEL_EXEC] = NET_SSH2_MSG_CHANNEL_REQUEST;
            if (!$this->get_channel_packet(self::CHANNEL_EXEC)) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                throw new RuntimeException('Unable to request pseudo-terminal');
            }
        }
        $packet = Strings::packSSH2('CNsCs', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL_EXEC], 'exec', 1, $command);
        $this->send_binary_packet($packet);
        $this->channel_status[self::CHANNEL_EXEC] = NET_SSH2_MSG_CHANNEL_REQUEST;
        if (!$this->get_channel_packet(self::CHANNEL_EXEC)) {
            return \false;
        }
        $this->channel_status[self::CHANNEL_EXEC] = NET_SSH2_MSG_CHANNEL_DATA;
        if ($this->request_pty === \true) {
            $this->channel_id_last_interactive = self::CHANNEL_EXEC;
            return \true;
        }
        $output = '';
        while (\true) {
            $temp = $this->get_channel_packet(self::CHANNEL_EXEC);
            switch (\true) {
                case $temp === \true:
                    return is_callable($callback) ? \true : $output;
                case $temp === \false:
                    return \false;
                default:
                    if (is_callable($callback)) {
                        if ($callback($temp) === \true) {
                            $this->close_channel(self::CHANNEL_EXEC);
                            return \true;
                        }
                    } else {
                        $output .= $temp;
                    }
            }
        }
    }
    public function getOpenChannelCount()
    {
        return $this->channelCount;
    }
    protected function open_channel($channel, $skip_extended = \false)
    {
        if (isset($this->channel_status[$channel]) && $this->channel_status[$channel] != NET_SSH2_MSG_CHANNEL_CLOSE) {
            throw new RuntimeException('Please close the channel (' . $channel . ') before trying to open it again');
        }
        $this->channelCount++;
        if ($this->channelCount > 1 && $this->errorOnMultipleChannels) {
            throw new RuntimeException("Ubuntu's OpenSSH from 5.8 to 6.9 doesn't work with multiple channels");
        }
        $this->window_size_server_to_client[$channel] = $this->window_size;
        $packet_size = 0x4000;
        $packet = Strings::packSSH2('CsN3', NET_SSH2_MSG_CHANNEL_OPEN, 'session', $channel, $this->window_size_server_to_client[$channel], $packet_size);
        $this->send_binary_packet($packet);
        $this->channel_status[$channel] = NET_SSH2_MSG_CHANNEL_OPEN;
        return $this->get_channel_packet($channel, $skip_extended);
    }
    public function openShell()
    {
        if (!$this->isAuthenticated()) {
            throw new InsufficientSetupException('Operation disallowed prior to login()');
        }
        $this->open_channel(self::CHANNEL_SHELL);
        $terminal_modes = pack('C', NET_SSH2_TTY_OP_END);
        $packet = Strings::packSSH2('CNsbsN4s', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL_SHELL], 'pty-req', \true, $this->term, $this->windowColumns, $this->windowRows, 0, 0, $terminal_modes);
        $this->send_binary_packet($packet);
        $this->channel_status[self::CHANNEL_SHELL] = NET_SSH2_MSG_CHANNEL_REQUEST;
        if (!$this->get_channel_packet(self::CHANNEL_SHELL)) {
            throw new RuntimeException('Unable to request pty');
        }
        $packet = Strings::packSSH2('CNsb', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL_SHELL], 'shell', \true);
        $this->send_binary_packet($packet);
        $response = $this->get_channel_packet(self::CHANNEL_SHELL);
        if ($response === \false) {
            throw new RuntimeException('Unable to request shell');
        }
        $this->channel_status[self::CHANNEL_SHELL] = NET_SSH2_MSG_CHANNEL_DATA;
        $this->channel_id_last_interactive = self::CHANNEL_SHELL;
        $this->bitmap |= self::MASK_SHELL;
        return \true;
    }
    private function get_interactive_channel()
    {
        switch (\true) {
            case $this->is_channel_status_data(self::CHANNEL_SUBSYSTEM):
                return self::CHANNEL_SUBSYSTEM;
            case $this->is_channel_status_data(self::CHANNEL_EXEC):
                return self::CHANNEL_EXEC;
            default:
                return self::CHANNEL_SHELL;
        }
    }
    private function is_channel_status_data($channel)
    {
        return isset($this->channel_status[$channel]) && $this->channel_status[$channel] == NET_SSH2_MSG_CHANNEL_DATA;
    }
    private function get_open_channel()
    {
        $channel = self::CHANNEL_EXEC;
        do {
            if (isset($this->channel_status[$channel]) && $this->channel_status[$channel] == NET_SSH2_MSG_CHANNEL_OPEN) {
                return $channel;
            }
        } while ($channel++ < self::CHANNEL_SUBSYSTEM);
        return \false;
    }
    public function requestAgentForwarding()
    {
        $request_channel = $this->get_open_channel();
        if ($request_channel === \false) {
            return \false;
        }
        $packet = Strings::packSSH2('CNsC', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[$request_channel], 'auth-agent-req@openssh.com', 1);
        $this->channel_status[$request_channel] = NET_SSH2_MSG_CHANNEL_REQUEST;
        $this->send_binary_packet($packet);
        if (!$this->get_channel_packet($request_channel)) {
            return \false;
        }
        $this->channel_status[$request_channel] = NET_SSH2_MSG_CHANNEL_OPEN;
        return \true;
    }
    public function read($expect = '', $mode = self::READ_SIMPLE, $channel = null)
    {
        if (!$this->isAuthenticated()) {
            throw new InsufficientSetupException('Operation disallowed prior to login()');
        }
        $this->curTimeout = $this->timeout;
        $this->is_timeout = \false;
        if ($channel === null) {
            $channel = $this->get_interactive_channel();
        }
        if (!$this->is_channel_status_data($channel) && empty($this->channel_buffers[$channel])) {
            if ($channel != self::CHANNEL_SHELL) {
                throw new InsufficientSetupException('Data is not available on channel');
            } elseif (!$this->openShell()) {
                throw new RuntimeException('Unable to initiate an interactive shell session');
            }
        }
        if ($mode == self::READ_NEXT) {
            return $this->get_channel_packet($channel);
        }
        $match = $expect;
        while (\true) {
            if ($mode == self::READ_REGEX) {
                preg_match($expect, substr($this->interactiveBuffer, -1024), $matches);
                $match = isset($matches[0]) ? $matches[0] : '';
            }
            $pos = strlen($match) ? strpos($this->interactiveBuffer, $match) : \false;
            if ($pos !== \false) {
                return Strings::shift($this->interactiveBuffer, $pos + strlen($match));
            }
            $response = $this->get_channel_packet($channel);
            if ($response === \true) {
                return Strings::shift($this->interactiveBuffer, strlen($this->interactiveBuffer));
            }
            $this->interactiveBuffer .= $response;
        }
    }
    public function write($cmd, $channel = null)
    {
        if (!$this->isAuthenticated()) {
            throw new InsufficientSetupException('Operation disallowed prior to login()');
        }
        if ($channel === null) {
            $channel = $this->get_interactive_channel();
        }
        if (!$this->is_channel_status_data($channel)) {
            if ($channel != self::CHANNEL_SHELL) {
                throw new InsufficientSetupException('Data is not available on channel');
            } elseif (!$this->openShell()) {
                throw new RuntimeException('Unable to initiate an interactive shell session');
            }
        }
        $this->send_channel_packet($channel, $cmd);
    }
    public function startSubsystem($subsystem)
    {
        $this->open_channel(self::CHANNEL_SUBSYSTEM);
        $packet = Strings::packSSH2('CNsCs', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL_SUBSYSTEM], 'subsystem', 1, $subsystem);
        $this->send_binary_packet($packet);
        $this->channel_status[self::CHANNEL_SUBSYSTEM] = NET_SSH2_MSG_CHANNEL_REQUEST;
        if (!$this->get_channel_packet(self::CHANNEL_SUBSYSTEM)) {
            return \false;
        }
        $this->channel_status[self::CHANNEL_SUBSYSTEM] = NET_SSH2_MSG_CHANNEL_DATA;
        $this->channel_id_last_interactive = self::CHANNEL_SUBSYSTEM;
        return \true;
    }
    public function stopSubsystem()
    {
        if ($this->isInteractiveChannelOpen(self::CHANNEL_SUBSYSTEM)) {
            $this->close_channel(self::CHANNEL_SUBSYSTEM);
        }
        return \true;
    }
    public function reset($channel = null)
    {
        if ($channel === null) {
            $channel = $this->get_interactive_channel();
        }
        if ($this->isInteractiveChannelOpen($channel)) {
            $this->close_channel($channel);
        }
    }
    public function isTimeout()
    {
        return $this->is_timeout;
    }
    public function disconnect()
    {
        $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
        if (isset($this->realtime_log_file) && is_resource($this->realtime_log_file)) {
            fclose($this->realtime_log_file);
        }
        unset(self::$connections[$this->getResourceId()]);
    }
    public function __destruct()
    {
        $this->disconnect();
    }
    public function isConnected($level = 0)
    {
        if (!is_int($level) || $level < 0 || $level > 2) {
            throw new InvalidArgumentException('$level must be 0, 1 or 2');
        }
        if ($level == 0) {
            return $this->bitmap & self::MASK_CONNECTED && is_resource($this->fsock) && !feof($this->fsock);
        }
        try {
            if ($level == 1) {
                $this->send_binary_packet(pack('CN', NET_SSH2_MSG_IGNORE, 0));
            } else {
                $this->open_channel(self::CHANNEL_KEEP_ALIVE);
                $this->close_channel(self::CHANNEL_KEEP_ALIVE);
            }
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    public function isAuthenticated()
    {
        return (bool) ($this->bitmap & self::MASK_LOGIN);
    }
    public function isShellOpen()
    {
        return $this->isInteractiveChannelOpen(self::CHANNEL_SHELL);
    }
    public function isPTYOpen()
    {
        return $this->isInteractiveChannelOpen(self::CHANNEL_EXEC);
    }
    public function isInteractiveChannelOpen($channel)
    {
        return $this->isAuthenticated() && $this->is_channel_status_data($channel);
    }
    public function getInteractiveChannelId()
    {
        return $this->channel_id_last_interactive;
    }
    public function ping()
    {
        if (!$this->isAuthenticated()) {
            if (!empty($this->auth)) {
                return $this->reconnect();
            }
            return \false;
        }
        try {
            $this->open_channel(self::CHANNEL_KEEP_ALIVE);
        } catch (RuntimeException $e) {
            return $this->reconnect();
        }
        $this->close_channel(self::CHANNEL_KEEP_ALIVE);
        return \true;
    }
    private function reconnect()
    {
        $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
        $this->connect();
        foreach ($this->auth as $auth) {
            $result = $this->login(...$auth);
        }
        return $result;
    }
    protected function reset_connection()
    {
        if (is_resource($this->fsock) && get_resource_type($this->fsock) === 'stream') {
            fclose($this->fsock);
        }
        $this->fsock = null;
        $this->bitmap = 0;
        $this->binary_packet_buffer = null;
        $this->decrypt = $this->encrypt = \false;
        $this->decrypt_block_size = $this->encrypt_block_size = 8;
        $this->hmac_check = $this->hmac_create = \false;
        $this->hmac_size = \false;
        $this->session_id = \false;
        $this->last_packet = null;
        $this->get_seq_no = $this->send_seq_no = 0;
        $this->channel_status = [];
        $this->channel_id_last_interactive = 0;
    }
    private function get_stream_timeout()
    {
        $sec = 0;
        $usec = 0;
        if ($this->curTimeout > 0) {
            $sec = (int) floor($this->curTimeout);
            $usec = (int) (1000000 * ($this->curTimeout - $sec));
        }
        if ($this->keepAlive > 0) {
            $elapsed = microtime(\true) - $this->last_packet;
            if ($elapsed < $this->curTimeout) {
                $sec = (int) floor($elapsed);
                $usec = (int) (1000000 * ($elapsed - $sec));
            }
        }
        return [$sec, $usec];
    }
    private function get_binary_packet()
    {
        if (!is_resource($this->fsock)) {
            throw new InvalidArgumentException('fsock is not a resource.');
        }
        if ($this->binary_packet_buffer == null) {
            $this->binary_packet_buffer = (object) ['raw' => '', 'plain' => '', 'packet_length' => null, 'size' => $this->decrypt_block_size];
        }
        $packet = $this->binary_packet_buffer;
        while (strlen($packet->raw) < $packet->size) {
            if (feof($this->fsock)) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_CONNECTION_LOST);
                throw new ConnectionClosedException('Connection closed by server');
            }
            if ($this->curTimeout < 0) {
                $this->is_timeout = \true;
                return \true;
            }
            $this->send_keep_alive();
            list($sec, $usec) = $this->get_stream_timeout();
            stream_set_timeout($this->fsock, $sec, $usec);
            $start = microtime(\true);
            $raw = stream_get_contents($this->fsock, $packet->size - strlen($packet->raw));
            $elapsed = microtime(\true) - $start;
            if ($this->curTimeout > 0) {
                $this->curTimeout -= $elapsed;
            }
            if ($raw === \false) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_CONNECTION_LOST);
                throw new ConnectionClosedException('Connection closed by server');
            } elseif (!strlen($raw)) {
                continue;
            }
            $packet->raw .= $raw;
            if (!$packet->packet_length) {
                $this->get_binary_packet_size($packet);
            }
        }
        if (strlen($packet->raw) != $packet->size) {
            throw new RuntimeException('Size of packet was not expected length');
        }
        $this->binary_packet_buffer = null;
        $raw = $packet->raw;
        if ($this->hmac_check instanceof Hash) {
            $hmac = Strings::pop($raw, $this->hmac_size);
        }
        $packet_length_header_size = 4;
        if ($this->decrypt) {
            switch ($this->decryptName) {
                case 'aes128-gcm@openssh.com':
                case 'aes256-gcm@openssh.com':
                    $this->decrypt->setNonce($this->decryptFixedPart . $this->decryptInvocationCounter);
                    Strings::increment_str($this->decryptInvocationCounter);
                    $this->decrypt->setAAD(Strings::shift($raw, $packet_length_header_size));
                    $this->decrypt->setTag(Strings::pop($raw, $this->decrypt_block_size));
                    $packet->plain = $this->decrypt->decrypt($raw);
                    break;
                case 'chacha20-poly1305@openssh.com':
                    if (!$this->decrypt instanceof ChaCha20) {
                        throw new LogicException('$this->decrypt is not a ' . ChaCha20::class);
                    }
                    $this->decrypt->setNonce(pack('N2', 0, $this->get_seq_no));
                    $this->decrypt->setCounter(0);
                    $this->decrypt->setPoly1305Key($this->decrypt->encrypt(str_repeat("\x00", 32)));
                    $this->decrypt->setAAD(Strings::shift($raw, $packet_length_header_size));
                    $this->decrypt->setCounter(1);
                    $this->decrypt->setTag(Strings::pop($raw, 16));
                    $packet->plain = $this->decrypt->decrypt($raw);
                    break;
                default:
                    if (!$this->hmac_check instanceof Hash || !$this->hmac_check_etm) {
                        Strings::shift($raw, $this->decrypt_block_size);
                        if (strlen($raw) > 0) {
                            $packet->plain .= $this->decrypt->decrypt($raw);
                        }
                    } else {
                        Strings::shift($raw, $packet_length_header_size);
                        $packet->plain = $this->decrypt->decrypt($raw);
                    }
                    break;
            }
        } else {
            Strings::shift($raw, $packet_length_header_size);
            $packet->plain = $raw;
        }
        if ($this->hmac_check instanceof Hash) {
            $reconstructed = (!$this->hmac_check_etm) ? pack('Na*', $packet->packet_length, $packet->plain) : substr($packet->raw, 0, -$this->hmac_size);
            if (($this->hmac_check->getHash() & "\xff\xff\xff\xff") == 'umac') {
                $this->hmac_check->setNonce("\x00\x00\x00\x00" . pack('N', $this->get_seq_no));
                if ($hmac != $this->hmac_check->hash($reconstructed)) {
                    $this->disconnect_helper(NET_SSH2_DISCONNECT_MAC_ERROR);
                    throw new ConnectionClosedException('Invalid UMAC');
                }
            } else if ($hmac != $this->hmac_check->hash(pack('Na*', $this->get_seq_no, $reconstructed))) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_MAC_ERROR);
                throw new ConnectionClosedException('Invalid HMAC');
            }
        }
        $padding_length = 0;
        $payload = $packet->plain;
        extract(unpack('Cpadding_length', Strings::shift($payload, 1)));
        if ($padding_length > 0) {
            Strings::pop($payload, $padding_length);
        }
        if (empty($payload)) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
            throw new ConnectionClosedException('Plaintext is too short');
        }
        switch ($this->decompress) {
            case self::NET_SSH2_COMPRESSION_ZLIB_AT_OPENSSH:
                if (!$this->isAuthenticated()) {
                    break;
                }
            case self::NET_SSH2_COMPRESSION_ZLIB:
                if ($this->regenerate_decompression_context) {
                    $this->regenerate_decompression_context = \false;
                    $cmf = ord($payload[0]);
                    $cm = $cmf & 0xf;
                    if ($cm != 8) {
                        user_error("Only CM = 8 ('deflate') is supported ({$cm})");
                    }
                    $cinfo = ($cmf & 0xf0) >> 4;
                    if ($cinfo > 7) {
                        user_error("CINFO above 7 is not allowed ({$cinfo})");
                    }
                    $windowSize = 1 << $cinfo + 8;
                    $flg = ord($payload[1]);
                    if (($cmf << 8 | $flg) % 31) {
                        user_error('fcheck failed');
                    }
                    $fdict = boolval($flg & 0x20);
                    $flevel = ($flg & 0xc0) >> 6;
                    $this->decompress_context = inflate_init(\ZLIB_ENCODING_RAW, ['window' => $cinfo + 8]);
                    $payload = substr($payload, 2);
                }
                if ($this->decompress_context) {
                    $payload = inflate_add($this->decompress_context, $payload, \ZLIB_PARTIAL_FLUSH);
                }
        }
        $this->get_seq_no++;
        if (defined('Staatic\Vendor\NET_SSH2_LOGGING')) {
            $current = microtime(\true);
            $message_number = isset(self::$message_numbers[ord($payload[0])]) ? self::$message_numbers[ord($payload[0])] : ('UNKNOWN (' . ord($payload[0]) . ')');
            $message_number = '<- ' . $message_number . ' (since last: ' . round($current - $this->last_packet, 4) . ', network: ' . round($elapsed, 4) . 's)';
            $this->append_log($message_number, $payload);
            $this->last_packet = $current;
        }
        return $this->filter($payload);
    }
    private function get_binary_packet_size(&$packet)
    {
        $packet_length_header_size = 4;
        if (strlen($packet->raw) < $packet_length_header_size) {
            return;
        }
        $packet_length = 0;
        $added_validation_length = 0;
        if ($this->decrypt) {
            switch ($this->decryptName) {
                case 'aes128-gcm@openssh.com':
                case 'aes256-gcm@openssh.com':
                    extract(unpack('Npacket_length', substr($packet->raw, 0, $packet_length_header_size)));
                    $packet->size = $packet_length_header_size + $packet_length + $this->decrypt_block_size;
                    break;
                case 'chacha20-poly1305@openssh.com':
                    $this->lengthDecrypt->setNonce(pack('N2', 0, $this->get_seq_no));
                    $packet_length_header = $this->lengthDecrypt->decrypt(substr($packet->raw, 0, $packet_length_header_size));
                    extract(unpack('Npacket_length', $packet_length_header));
                    $packet->size = $packet_length_header_size + $packet_length + 16;
                    break;
                default:
                    if (!$this->hmac_check instanceof Hash || !$this->hmac_check_etm) {
                        if (strlen($packet->raw) < $this->decrypt_block_size) {
                            return;
                        }
                        $packet->plain = $this->decrypt->decrypt(substr($packet->raw, 0, $this->decrypt_block_size));
                        extract(unpack('Npacket_length', Strings::shift($packet->plain, $packet_length_header_size)));
                        $packet->size = $packet_length_header_size + $packet_length;
                        $added_validation_length = $packet_length_header_size;
                    } else {
                        extract(unpack('Npacket_length', substr($packet->raw, 0, $packet_length_header_size)));
                        $packet->size = $packet_length_header_size + $packet_length;
                    }
                    break;
            }
        } else {
            extract(unpack('Npacket_length', substr($packet->raw, 0, $packet_length_header_size)));
            $packet->size = $packet_length_header_size + $packet_length;
            $added_validation_length = $packet_length_header_size;
        }
        if ($packet_length <= 0 || $packet_length > 0x9000 || ($packet_length + $added_validation_length) % $this->decrypt_block_size != 0) {
            $this->disconnect_helper(NET_SSH2_DISCONNECT_PROTOCOL_ERROR);
            throw new InvalidPacketLengthException('Invalid packet length');
        }
        if ($this->hmac_check instanceof Hash) {
            $packet->size += $this->hmac_size;
        }
        $packet->packet_length = $packet_length;
    }
    private function filter($payload)
    {
        switch (ord($payload[0])) {
            case NET_SSH2_MSG_DISCONNECT:
                Strings::shift($payload, 1);
                list($reason_code, $message) = Strings::unpackSSH2('Ns', $payload);
                $this->errors[] = 'SSH_MSG_DISCONNECT: ' . self::$disconnect_reasons[$reason_code] . "\r\n{$message}";
                $this->bitmap = 0;
                return \false;
            case NET_SSH2_MSG_IGNORE:
                $this->extra_packets++;
                $payload = $this->get_binary_packet();
                break;
            case NET_SSH2_MSG_DEBUG:
                $this->extra_packets++;
                Strings::shift($payload, 2);
                list($message) = Strings::unpackSSH2('s', $payload);
                $this->errors[] = "SSH_MSG_DEBUG: {$message}";
                $payload = $this->get_binary_packet();
                break;
            case NET_SSH2_MSG_UNIMPLEMENTED:
                return \false;
            case NET_SSH2_MSG_KEXINIT:
                if ($this->session_id !== \false) {
                    if (!$this->key_exchange($payload)) {
                        $this->bitmap = 0;
                        return \false;
                    }
                    $payload = $this->get_binary_packet();
                }
                break;
            case NET_SSH2_MSG_EXT_INFO:
                Strings::shift($payload, 1);
                list($nr_extensions) = Strings::unpackSSH2('N', $payload);
                for ($i = 0; $i < $nr_extensions; $i++) {
                    list($extension_name, $extension_value) = Strings::unpackSSH2('ss', $payload);
                    if ($extension_name == 'server-sig-algs') {
                        $this->supported_private_key_algorithms = explode(',', $extension_value);
                    }
                }
                $payload = $this->get_binary_packet();
        }
        if ($this->bitmap & self::MASK_CONNECTED && !$this->isAuthenticated() && !is_bool($payload) && ord($payload[0]) == NET_SSH2_MSG_USERAUTH_BANNER) {
            Strings::shift($payload, 1);
            list($this->banner_message) = Strings::unpackSSH2('s', $payload);
            $payload = $this->get_binary_packet();
        }
        if ($this->bitmap & self::MASK_CONNECTED && $this->isAuthenticated()) {
            if (is_bool($payload)) {
                return $payload;
            }
            switch (ord($payload[0])) {
                case NET_SSH2_MSG_CHANNEL_REQUEST:
                    if (strlen($payload) == 31) {
                        extract(unpack('cpacket_type/Nchannel/Nlength', $payload));
                        if (substr($payload, 9, $length) == 'keepalive@openssh.com' && isset($this->server_channels[$channel])) {
                            if (ord(substr($payload, 9 + $length))) {
                                $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_SUCCESS, $this->server_channels[$channel]));
                            }
                            $payload = $this->get_binary_packet();
                        }
                    }
                    break;
                case NET_SSH2_MSG_GLOBAL_REQUEST:
                    Strings::shift($payload, 1);
                    list($request_name) = Strings::unpackSSH2('s', $payload);
                    $this->errors[] = "SSH_MSG_GLOBAL_REQUEST: {$request_name}";
                    try {
                        $this->send_binary_packet(pack('C', NET_SSH2_MSG_REQUEST_FAILURE));
                    } catch (RuntimeException $e) {
                        return $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                    }
                    $payload = $this->get_binary_packet();
                    break;
                case NET_SSH2_MSG_CHANNEL_OPEN:
                    Strings::shift($payload, 1);
                    list($data, $server_channel) = Strings::unpackSSH2('sN', $payload);
                    switch ($data) {
                        case 'auth-agent':
                        case 'auth-agent@openssh.com':
                            if (isset($this->agent)) {
                                $new_channel = self::CHANNEL_AGENT_FORWARD;
                                list($remote_window_size, $remote_maximum_packet_size) = Strings::unpackSSH2('NN', $payload);
                                $this->packet_size_client_to_server[$new_channel] = $remote_window_size;
                                $this->window_size_server_to_client[$new_channel] = $remote_maximum_packet_size;
                                $this->window_size_client_to_server[$new_channel] = $this->window_size;
                                $packet_size = 0x4000;
                                $packet = pack('CN4', NET_SSH2_MSG_CHANNEL_OPEN_CONFIRMATION, $server_channel, $new_channel, $packet_size, $packet_size);
                                $this->server_channels[$new_channel] = $server_channel;
                                $this->channel_status[$new_channel] = NET_SSH2_MSG_CHANNEL_OPEN_CONFIRMATION;
                                $this->send_binary_packet($packet);
                            }
                            break;
                        default:
                            $packet = Strings::packSSH2('CN2ss', NET_SSH2_MSG_CHANNEL_OPEN_FAILURE, $server_channel, NET_SSH2_OPEN_ADMINISTRATIVELY_PROHIBITED, '', '');
                            try {
                                $this->send_binary_packet($packet);
                            } catch (RuntimeException $e) {
                                return $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                            }
                    }
                    $payload = $this->get_binary_packet();
                    break;
                case NET_SSH2_MSG_CHANNEL_WINDOW_ADJUST:
                    Strings::shift($payload, 1);
                    list($channel, $window_size) = Strings::unpackSSH2('NN', $payload);
                    $this->window_size_client_to_server[$channel] += $window_size;
                    $payload = ($this->bitmap & self::MASK_WINDOW_ADJUST) ? \true : $this->get_binary_packet();
            }
        }
        return $payload;
    }
    public function enableQuietMode()
    {
        $this->quiet_mode = \true;
    }
    public function disableQuietMode()
    {
        $this->quiet_mode = \false;
    }
    public function isQuietModeEnabled()
    {
        return $this->quiet_mode;
    }
    public function enablePTY()
    {
        $this->request_pty = \true;
    }
    public function disablePTY()
    {
        if ($this->isPTYOpen()) {
            $this->close_channel(self::CHANNEL_EXEC);
        }
        $this->request_pty = \false;
    }
    public function isPTYEnabled()
    {
        return $this->request_pty;
    }
    protected function get_channel_packet($client_channel, $skip_extended = \false)
    {
        if (!empty($this->channel_buffers[$client_channel])) {
            switch ($this->channel_status[$client_channel]) {
                case NET_SSH2_MSG_CHANNEL_REQUEST:
                    foreach ($this->channel_buffers[$client_channel] as $i => $packet) {
                        switch (ord($packet[0])) {
                            case NET_SSH2_MSG_CHANNEL_SUCCESS:
                            case NET_SSH2_MSG_CHANNEL_FAILURE:
                                unset($this->channel_buffers[$client_channel][$i]);
                                return substr($packet, 1);
                        }
                    }
                    break;
                default:
                    return substr(array_shift($this->channel_buffers[$client_channel]), 1);
            }
        }
        while (\true) {
            $response = $this->get_binary_packet();
            if ($response === \true && $this->is_timeout) {
                if ($client_channel == self::CHANNEL_EXEC && !$this->request_pty) {
                    $this->close_channel($client_channel);
                }
                return \true;
            }
            if ($response === \false) {
                $this->disconnect_helper(NET_SSH2_DISCONNECT_CONNECTION_LOST);
                throw new ConnectionClosedException('Connection closed by server');
            }
            if ($client_channel == -1 && $response === \true) {
                return \true;
            }
            list($type, $channel) = Strings::unpackSSH2('CN', $response);
            if (isset($channel) && isset($this->channel_status[$channel]) && isset($this->window_size_server_to_client[$channel])) {
                $this->window_size_server_to_client[$channel] -= strlen($response);
                if ($this->window_size_server_to_client[$channel] < 0) {
                    $packet = pack('CNN', NET_SSH2_MSG_CHANNEL_WINDOW_ADJUST, $this->server_channels[$channel], $this->window_resize);
                    $this->send_binary_packet($packet);
                    $this->window_size_server_to_client[$channel] += $this->window_resize;
                }
                switch ($type) {
                    case NET_SSH2_MSG_CHANNEL_EXTENDED_DATA:
                        list($data_type_code, $data) = Strings::unpackSSH2('Ns', $response);
                        $this->stdErrorLog .= $data;
                        if ($skip_extended || $this->quiet_mode) {
                            continue 2;
                        }
                        if ($client_channel == $channel && $this->channel_status[$channel] == NET_SSH2_MSG_CHANNEL_DATA) {
                            return $data;
                        }
                        $this->channel_buffers[$channel][] = chr($type) . $data;
                        continue 2;
                    case NET_SSH2_MSG_CHANNEL_REQUEST:
                        if ($this->channel_status[$channel] == NET_SSH2_MSG_CHANNEL_CLOSE) {
                            continue 2;
                        }
                        list($value) = Strings::unpackSSH2('s', $response);
                        switch ($value) {
                            case 'exit-signal':
                                list(, $signal_name, , $error_message) = Strings::unpackSSH2('bsbs', $response);
                                $this->errors[] = "SSH_MSG_CHANNEL_REQUEST (exit-signal): {$signal_name}";
                                if (strlen($error_message)) {
                                    $this->errors[count($this->errors) - 1] .= "\r\n{$error_message}";
                                }
                                $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_EOF, $this->server_channels[$client_channel]));
                                $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_CLOSE, $this->server_channels[$channel]));
                                $this->channel_status[$channel] = NET_SSH2_MSG_CHANNEL_EOF;
                                continue 3;
                            case 'exit-status':
                                list(, $this->exit_status) = Strings::unpackSSH2('CN', $response);
                                continue 3;
                            default:
                                continue 3;
                        }
                }
                switch ($this->channel_status[$channel]) {
                    case NET_SSH2_MSG_CHANNEL_OPEN:
                        switch ($type) {
                            case NET_SSH2_MSG_CHANNEL_OPEN_CONFIRMATION:
                                list($this->server_channels[$channel], $window_size, $this->packet_size_client_to_server[$channel]) = Strings::unpackSSH2('NNN', $response);
                                if ($window_size < 0) {
                                    $window_size &= 0x7fffffff;
                                    $window_size += 0x80000000;
                                }
                                $this->window_size_client_to_server[$channel] = $window_size;
                                $result = ($client_channel == $channel) ? \true : $this->get_channel_packet($client_channel, $skip_extended);
                                $this->on_channel_open();
                                return $result;
                            case NET_SSH2_MSG_CHANNEL_OPEN_FAILURE:
                                $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                                throw new RuntimeException('Unable to open channel');
                            default:
                                if ($client_channel == $channel) {
                                    $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                                    throw new RuntimeException('Unexpected response to open request');
                                }
                                return $this->get_channel_packet($client_channel, $skip_extended);
                        }
                        break;
                    case NET_SSH2_MSG_CHANNEL_REQUEST:
                        switch ($type) {
                            case NET_SSH2_MSG_CHANNEL_SUCCESS:
                                return \true;
                            case NET_SSH2_MSG_CHANNEL_FAILURE:
                                return \false;
                            case NET_SSH2_MSG_CHANNEL_DATA:
                                list($data) = Strings::unpackSSH2('s', $response);
                                $this->channel_buffers[$channel][] = chr($type) . $data;
                                return $this->get_channel_packet($client_channel, $skip_extended);
                            default:
                                $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                                throw new RuntimeException('Unable to fulfill channel request');
                        }
                    case NET_SSH2_MSG_CHANNEL_CLOSE:
                        if ($client_channel == $channel && $type == NET_SSH2_MSG_CHANNEL_CLOSE) {
                            return \true;
                        }
                        return $this->get_channel_packet($client_channel, $skip_extended);
                }
            }
            switch ($type) {
                case NET_SSH2_MSG_CHANNEL_DATA:
                    list($data) = Strings::unpackSSH2('s', $response);
                    if ($channel == self::CHANNEL_AGENT_FORWARD) {
                        $agent_response = $this->agent->forwardData($data);
                        if (!is_bool($agent_response)) {
                            $this->send_channel_packet($channel, $agent_response);
                        }
                        break;
                    }
                    if ($client_channel == $channel) {
                        return $data;
                    }
                    $this->channel_buffers[$channel][] = chr($type) . $data;
                    break;
                case NET_SSH2_MSG_CHANNEL_CLOSE:
                    $this->curTimeout = 5;
                    $this->close_channel_bitmap($channel);
                    if ($this->channel_status[$channel] != NET_SSH2_MSG_CHANNEL_EOF) {
                        $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_CLOSE, $this->server_channels[$channel]));
                    }
                    $this->channel_status[$channel] = NET_SSH2_MSG_CHANNEL_CLOSE;
                    $this->channelCount--;
                    if ($client_channel == $channel) {
                        return \true;
                    }
                case NET_SSH2_MSG_CHANNEL_EOF:
                    break;
                default:
                    $this->disconnect_helper(NET_SSH2_DISCONNECT_BY_APPLICATION);
                    throw new RuntimeException("Error reading channel data ({$type})");
            }
        }
    }
    protected function send_binary_packet($data, $logged = null)
    {
        if (!is_resource($this->fsock) || feof($this->fsock)) {
            $this->bitmap = 0;
            throw new ConnectionClosedException('Connection closed prematurely');
        }
        if (!isset($logged)) {
            $logged = $data;
        }
        switch ($this->compress) {
            case self::NET_SSH2_COMPRESSION_ZLIB_AT_OPENSSH:
                if (!$this->isAuthenticated()) {
                    break;
                }
            case self::NET_SSH2_COMPRESSION_ZLIB:
                if (!$this->regenerate_compression_context) {
                    $header = '';
                } else {
                    $this->regenerate_compression_context = \false;
                    $this->compress_context = deflate_init(\ZLIB_ENCODING_RAW, ['window' => 15]);
                    $header = "x\x9c";
                }
                if ($this->compress_context) {
                    $data = $header . deflate_add($this->compress_context, $data, \ZLIB_PARTIAL_FLUSH);
                }
        }
        $packet_length = strlen($data) + 9;
        if ($this->encrypt && $this->encrypt->usesNonce()) {
            $packet_length -= 4;
        }
        $packet_length += ($this->encrypt_block_size - 1) * $packet_length % $this->encrypt_block_size;
        $padding_length = $packet_length - strlen($data) - 5;
        switch (\true) {
            case $this->encrypt && $this->encrypt->usesNonce():
            case $this->hmac_create instanceof Hash && $this->hmac_create_etm:
                $padding_length += 4;
                $packet_length += 4;
        }
        $padding = Random::string($padding_length);
        $packet = pack('NCa*', $packet_length - 4, $padding_length, $data . $padding);
        $hmac = '';
        if ($this->hmac_create instanceof Hash && !$this->hmac_create_etm) {
            if (($this->hmac_create->getHash() & "\xff\xff\xff\xff") == 'umac') {
                $this->hmac_create->setNonce("\x00\x00\x00\x00" . pack('N', $this->send_seq_no));
                $hmac = $this->hmac_create->hash($packet);
            } else {
                $hmac = $this->hmac_create->hash(pack('Na*', $this->send_seq_no, $packet));
            }
        }
        if ($this->encrypt) {
            switch ($this->encryptName) {
                case 'aes128-gcm@openssh.com':
                case 'aes256-gcm@openssh.com':
                    $this->encrypt->setNonce($this->encryptFixedPart . $this->encryptInvocationCounter);
                    Strings::increment_str($this->encryptInvocationCounter);
                    $this->encrypt->setAAD($temp = $packet & "\xff\xff\xff\xff");
                    $packet = $temp . $this->encrypt->encrypt(substr($packet, 4));
                    break;
                case 'chacha20-poly1305@openssh.com':
                    if (!$this->encrypt instanceof ChaCha20) {
                        throw new LogicException('$this->encrypt is not a ' . ChaCha20::class);
                    }
                    $nonce = pack('N2', 0, $this->send_seq_no);
                    $this->encrypt->setNonce($nonce);
                    $this->lengthEncrypt->setNonce($nonce);
                    $length = $this->lengthEncrypt->encrypt($packet & "\xff\xff\xff\xff");
                    $this->encrypt->setCounter(0);
                    $this->encrypt->setPoly1305Key($this->encrypt->encrypt(str_repeat("\x00", 32)));
                    $this->encrypt->setAAD($length);
                    $this->encrypt->setCounter(1);
                    $packet = $length . $this->encrypt->encrypt(substr($packet, 4));
                    break;
                default:
                    $packet = ($this->hmac_create instanceof Hash && $this->hmac_create_etm) ? ($packet & "\xff\xff\xff\xff") . $this->encrypt->encrypt(substr($packet, 4)) : $this->encrypt->encrypt($packet);
            }
        }
        if ($this->hmac_create instanceof Hash && $this->hmac_create_etm) {
            if (($this->hmac_create->getHash() & "\xff\xff\xff\xff") == 'umac') {
                $this->hmac_create->setNonce("\x00\x00\x00\x00" . pack('N', $this->send_seq_no));
                $hmac = $this->hmac_create->hash($packet);
            } else {
                $hmac = $this->hmac_create->hash(pack('Na*', $this->send_seq_no, $packet));
            }
        }
        $this->send_seq_no++;
        $packet .= ($this->encrypt && $this->encrypt->usesNonce()) ? $this->encrypt->getTag() : $hmac;
        $start = microtime(\true);
        $sent = @fputs($this->fsock, $packet);
        $stop = microtime(\true);
        if (defined('Staatic\Vendor\NET_SSH2_LOGGING')) {
            $current = microtime(\true);
            $message_number = isset(self::$message_numbers[ord($logged[0])]) ? self::$message_numbers[ord($logged[0])] : ('UNKNOWN (' . ord($logged[0]) . ')');
            $message_number = '-> ' . $message_number . ' (since last: ' . round($current - $this->last_packet, 4) . ', network: ' . round($stop - $start, 4) . 's)';
            $this->append_log($message_number, $logged);
            $this->last_packet = $current;
        }
        if (strlen($packet) != $sent) {
            $this->bitmap = 0;
            $message = ($sent === \false) ? 'Unable to write ' . strlen($packet) . ' bytes' : ("Only {$sent} of " . strlen($packet) . " bytes were sent");
            throw new RuntimeException($message);
        }
    }
    private function send_keep_alive()
    {
        if ($this->bitmap & self::MASK_CONNECTED) {
            $elapsed = microtime(\true) - $this->last_packet;
            if ($this->keepAlive > 0 && $elapsed >= $this->keepAlive) {
                $this->send_binary_packet(pack('CN', NET_SSH2_MSG_IGNORE, 0));
            }
        }
    }
    private function append_log($message_number, $message)
    {
        $this->append_log_helper(NET_SSH2_LOGGING, $message_number, $message, $this->message_number_log, $this->message_log, $this->log_size, $this->realtime_log_file, $this->realtime_log_wrap, $this->realtime_log_size);
    }
    /**
     * @param mixed[] $message_number_log
     * @param mixed[] $message_log
     */
    protected function append_log_helper($constant, $message_number, $message, &$message_number_log, &$message_log, &$log_size, &$realtime_log_file, &$realtime_log_wrap, &$realtime_log_size)
    {
        if (strlen($message_number) > 2) {
            Strings::shift($message);
        }
        switch ($constant) {
            case self::LOG_SIMPLE:
                $message_number_log[] = $message_number;
                break;
            case self::LOG_SIMPLE_REALTIME:
                echo $message_number;
                echo (\PHP_SAPI == 'cli') ? "\r\n" : '<br>';
                @flush();
                @ob_flush();
                break;
            case self::LOG_COMPLEX:
                $message_number_log[] = $message_number;
                $log_size += strlen($message);
                $message_log[] = $message;
                while ($log_size > self::LOG_MAX_SIZE) {
                    $log_size -= strlen(array_shift($message_log));
                    array_shift($message_number_log);
                }
                break;
            case self::LOG_REALTIME:
                switch (\PHP_SAPI) {
                    case 'cli':
                        $start = $stop = "\r\n";
                        break;
                    default:
                        $start = '<pre>';
                        $stop = '</pre>';
                }
                echo $start . $this->format_log([$message], [$message_number]) . $stop;
                @flush();
                @ob_flush();
                break;
            case self::LOG_REALTIME_FILE:
                if (!isset($realtime_log_file)) {
                    $filename = NET_SSH2_LOG_REALTIME_FILENAME;
                    $fp = fopen($filename, 'w');
                    $realtime_log_file = $fp;
                }
                if (!is_resource($realtime_log_file)) {
                    break;
                }
                $entry = $this->format_log([$message], [$message_number]);
                if ($realtime_log_wrap) {
                    $temp = "<<< START >>>\r\n";
                    $entry .= $temp;
                    fseek($realtime_log_file, ftell($realtime_log_file) - strlen($temp));
                }
                $realtime_log_size += strlen($entry);
                if ($realtime_log_size > self::LOG_MAX_SIZE) {
                    fseek($realtime_log_file, 0);
                    $realtime_log_size = strlen($entry);
                    $realtime_log_wrap = \true;
                }
                fputs($realtime_log_file, $entry);
        }
    }
    protected function send_channel_packet($client_channel, $data)
    {
        while (strlen($data)) {
            if (!$this->window_size_client_to_server[$client_channel]) {
                $this->bitmap ^= self::MASK_WINDOW_ADJUST;
                $this->get_channel_packet(-1);
                $this->bitmap ^= self::MASK_WINDOW_ADJUST;
            }
            $max_size = min($this->packet_size_client_to_server[$client_channel], $this->window_size_client_to_server[$client_channel]);
            $temp = Strings::shift($data, $max_size);
            $packet = Strings::packSSH2('CNs', NET_SSH2_MSG_CHANNEL_DATA, $this->server_channels[$client_channel], $temp);
            $this->window_size_client_to_server[$client_channel] -= strlen($temp);
            $this->send_binary_packet($packet);
        }
    }
    private function close_channel($client_channel, $want_reply = \false)
    {
        $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_EOF, $this->server_channels[$client_channel]));
        if (!$want_reply) {
            $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_CLOSE, $this->server_channels[$client_channel]));
        }
        $this->channel_status[$client_channel] = NET_SSH2_MSG_CHANNEL_CLOSE;
        $this->channelCount--;
        $this->curTimeout = 5;
        while (!is_bool($this->get_channel_packet($client_channel))) {
        }
        if ($want_reply) {
            $this->send_binary_packet(pack('CN', NET_SSH2_MSG_CHANNEL_CLOSE, $this->server_channels[$client_channel]));
        }
        $this->close_channel_bitmap($client_channel);
    }
    private function close_channel_bitmap($client_channel)
    {
        switch ($client_channel) {
            case self::CHANNEL_SHELL:
                if ($this->bitmap & self::MASK_SHELL) {
                    $this->bitmap &= ~self::MASK_SHELL;
                }
                break;
        }
    }
    protected function disconnect_helper($reason)
    {
        if ($this->bitmap & self::MASK_CONNECTED) {
            $data = Strings::packSSH2('CNss', NET_SSH2_MSG_DISCONNECT, $reason, '', '');
            try {
                $this->send_binary_packet($data);
            } catch (Exception $e) {
            }
        }
        $this->reset_connection();
        return \false;
    }
    protected static function define_array(...$args)
    {
        foreach ($args as $arg) {
            foreach ($arg as $key => $value) {
                if (!defined($value)) {
                    define($value, $key);
                } else {
                    break 2;
                }
            }
        }
    }
    public function getLog()
    {
        if (!defined('Staatic\Vendor\NET_SSH2_LOGGING')) {
            return \false;
        }
        switch (NET_SSH2_LOGGING) {
            case self::LOG_SIMPLE:
                return $this->message_number_log;
            case self::LOG_COMPLEX:
                $log = $this->format_log($this->message_log, $this->message_number_log);
                return (\PHP_SAPI == 'cli') ? $log : ('<pre>' . $log . '</pre>');
            default:
                return \false;
        }
    }
    /**
     * @param mixed[] $message_log
     * @param mixed[] $message_number_log
     */
    protected function format_log($message_log, $message_number_log)
    {
        $output = '';
        for ($i = 0; $i < count($message_log); $i++) {
            $output .= $message_number_log[$i] . "\r\n";
            $current_log = $message_log[$i];
            $j = 0;
            do {
                if (strlen($current_log)) {
                    $output .= str_pad(dechex($j), 7, '0', \STR_PAD_LEFT) . '0  ';
                }
                $fragment = Strings::shift($current_log, $this->log_short_width);
                $hex = substr(preg_replace_callback('#.#s', function ($matches) {
                    return $this->log_boundary . str_pad(dechex(ord($matches[0])), 2, '0', \STR_PAD_LEFT);
                }, $fragment), strlen($this->log_boundary));
                $raw = preg_replace('#[^\x20-\x7E]|<#', '.', $fragment);
                $output .= str_pad($hex, $this->log_long_width - $this->log_short_width, ' ') . $raw . "\r\n";
                $j++;
            } while (strlen($current_log));
            $output .= "\r\n";
        }
        return $output;
    }
    private function on_channel_open()
    {
        if (isset($this->agent)) {
            $this->agent->registerChannelOpen($this);
        }
    }
    private static function array_intersect_first(array $array1, array $array2)
    {
        foreach ($array1 as $value) {
            if (in_array($value, $array2)) {
                return $value;
            }
        }
        return \false;
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function getLastError()
    {
        $count = count($this->errors);
        if ($count > 0) {
            return $this->errors[$count - 1];
        }
    }
    public function getServerIdentification()
    {
        $this->connect();
        return $this->server_identifier;
    }
    public function getServerAlgorithms()
    {
        $this->connect();
        return ['kex' => $this->kex_algorithms, 'hostkey' => $this->server_host_key_algorithms, 'client_to_server' => ['crypt' => $this->encryption_algorithms_client_to_server, 'mac' => $this->mac_algorithms_client_to_server, 'comp' => $this->compression_algorithms_client_to_server, 'lang' => $this->languages_client_to_server], 'server_to_client' => ['crypt' => $this->encryption_algorithms_server_to_client, 'mac' => $this->mac_algorithms_server_to_client, 'comp' => $this->compression_algorithms_server_to_client, 'lang' => $this->languages_server_to_client]];
    }
    public static function getSupportedKEXAlgorithms()
    {
        $kex_algorithms = ['curve25519-sha256', 'curve25519-sha256@libssh.org', 'ecdh-sha2-nistp256', 'ecdh-sha2-nistp384', 'ecdh-sha2-nistp521', 'diffie-hellman-group-exchange-sha256', 'diffie-hellman-group-exchange-sha1', 'diffie-hellman-group14-sha256', 'diffie-hellman-group14-sha1', 'diffie-hellman-group15-sha512', 'diffie-hellman-group16-sha512', 'diffie-hellman-group17-sha512', 'diffie-hellman-group18-sha512', 'diffie-hellman-group1-sha1'];
        return $kex_algorithms;
    }
    public static function getSupportedHostKeyAlgorithms()
    {
        return ['ssh-ed25519', 'ecdsa-sha2-nistp256', 'ecdsa-sha2-nistp384', 'ecdsa-sha2-nistp521', 'rsa-sha2-256', 'rsa-sha2-512', 'ssh-rsa', 'ssh-dss'];
    }
    public static function getSupportedEncryptionAlgorithms()
    {
        $algos = ['aes128-gcm@openssh.com', 'aes256-gcm@openssh.com', 'arcfour256', 'arcfour128', 'aes128-ctr', 'aes192-ctr', 'aes256-ctr', 'chacha20-poly1305@openssh.com', 'twofish128-ctr', 'twofish192-ctr', 'twofish256-ctr', 'aes128-cbc', 'aes192-cbc', 'aes256-cbc', 'twofish128-cbc', 'twofish192-cbc', 'twofish256-cbc', 'twofish-cbc', 'blowfish-ctr', 'blowfish-cbc', '3des-ctr', '3des-cbc'];
        if (self::$crypto_engine) {
            $engines = [self::$crypto_engine];
        } else {
            $engines = ['libsodium', 'OpenSSL (GCM)', 'OpenSSL', 'mcrypt', 'Eval', 'PHP'];
        }
        $ciphers = [];
        foreach ($engines as $engine) {
            foreach ($algos as $algo) {
                $obj = self::encryption_algorithm_to_crypt_instance($algo);
                if ($obj instanceof Rijndael) {
                    $obj->setKeyLength(preg_replace('#[^\d]#', '', $algo));
                }
                switch ($algo) {
                    case 'chacha20-poly1305@openssh.com':
                    case 'arcfour128':
                    case 'arcfour256':
                        if ($engine != 'PHP') {
                            continue 2;
                        }
                        break;
                    case 'aes128-gcm@openssh.com':
                    case 'aes256-gcm@openssh.com':
                        if ($engine == 'OpenSSL') {
                            continue 2;
                        }
                        $obj->setNonce('dummydummydu');
                }
                if ($obj->isValidEngine($engine)) {
                    $algos = array_diff($algos, [$algo]);
                    $ciphers[] = $algo;
                }
            }
        }
        return $ciphers;
    }
    public static function getSupportedMACAlgorithms()
    {
        return ['hmac-sha2-256-etm@openssh.com', 'hmac-sha2-512-etm@openssh.com', 'umac-64-etm@openssh.com', 'umac-128-etm@openssh.com', 'hmac-sha1-etm@openssh.com', 'hmac-sha2-256', 'hmac-sha2-512', 'umac-64@openssh.com', 'umac-128@openssh.com', 'hmac-sha1-96', 'hmac-sha1', 'hmac-md5-96', 'hmac-md5'];
    }
    public static function getSupportedCompressionAlgorithms()
    {
        $algos = ['none'];
        if (function_exists('deflate_init')) {
            $algos[] = 'zlib@openssh.com';
            $algos[] = 'zlib';
        }
        return $algos;
    }
    public function getAlgorithmsNegotiated()
    {
        $this->connect();
        $compression_map = [self::NET_SSH2_COMPRESSION_NONE => 'none', self::NET_SSH2_COMPRESSION_ZLIB => 'zlib', self::NET_SSH2_COMPRESSION_ZLIB_AT_OPENSSH => 'zlib@openssh.com'];
        return ['kex' => $this->kex_algorithm, 'hostkey' => $this->signature_format, 'client_to_server' => ['crypt' => $this->encryptName, 'mac' => $this->hmac_create_name, 'comp' => $compression_map[$this->compress]], 'server_to_client' => ['crypt' => $this->decryptName, 'mac' => $this->hmac_check_name, 'comp' => $compression_map[$this->decompress]]];
    }
    public function forceMultipleChannels()
    {
        $this->errorOnMultipleChannels = \false;
    }
    public function setTerminal($term)
    {
        $this->term = $term;
    }
    /**
     * @param mixed[] $methods
     */
    public function setPreferredAlgorithms($methods)
    {
        $preferred = $methods;
        if (isset($preferred['kex'])) {
            $preferred['kex'] = array_intersect($preferred['kex'], static::getSupportedKEXAlgorithms());
        }
        if (isset($preferred['hostkey'])) {
            $preferred['hostkey'] = array_intersect($preferred['hostkey'], static::getSupportedHostKeyAlgorithms());
        }
        $keys = ['client_to_server', 'server_to_client'];
        foreach ($keys as $key) {
            if (isset($preferred[$key])) {
                $a =& $preferred[$key];
                if (isset($a['crypt'])) {
                    $a['crypt'] = array_intersect($a['crypt'], static::getSupportedEncryptionAlgorithms());
                }
                if (isset($a['comp'])) {
                    $a['comp'] = array_intersect($a['comp'], static::getSupportedCompressionAlgorithms());
                }
                if (isset($a['mac'])) {
                    $a['mac'] = array_intersect($a['mac'], static::getSupportedMACAlgorithms());
                }
            }
        }
        $keys = ['kex', 'hostkey', 'client_to_server/crypt', 'client_to_server/comp', 'client_to_server/mac', 'server_to_client/crypt', 'server_to_client/comp', 'server_to_client/mac'];
        foreach ($keys as $key) {
            $p = $preferred;
            $m = $methods;
            $subkeys = explode('/', $key);
            foreach ($subkeys as $subkey) {
                if (!isset($p[$subkey])) {
                    continue 2;
                }
                $p = $p[$subkey];
                $m = $m[$subkey];
            }
            if (count($p) != count($m)) {
                $diff = array_diff($m, $p);
                $msg = (count($diff) == 1) ? ' is not a supported algorithm' : ' are not supported algorithms';
                throw new UnsupportedAlgorithmException(implode(', ', $diff) . $msg);
            }
        }
        $this->preferred = $preferred;
    }
    public function getBannerMessage()
    {
        return $this->banner_message;
    }
    public function getServerPublicHostKey()
    {
        if (!($this->bitmap & self::MASK_CONSTRUCTOR)) {
            $this->connect();
        }
        $signature = $this->signature;
        $server_public_host_key = base64_encode($this->server_public_host_key);
        if ($this->signature_validated) {
            return $this->bitmap ? $this->signature_format . ' ' . $server_public_host_key : \false;
        }
        $this->signature_validated = \true;
        switch ($this->signature_format) {
            case 'ssh-ed25519':
            case 'ecdsa-sha2-nistp256':
            case 'ecdsa-sha2-nistp384':
            case 'ecdsa-sha2-nistp521':
                $key = EC::loadFormat('OpenSSH', $server_public_host_key)->withSignatureFormat('SSH2');
                switch ($this->signature_format) {
                    case 'ssh-ed25519':
                        $hash = 'sha512';
                        break;
                    case 'ecdsa-sha2-nistp256':
                        $hash = 'sha256';
                        break;
                    case 'ecdsa-sha2-nistp384':
                        $hash = 'sha384';
                        break;
                    case 'ecdsa-sha2-nistp521':
                        $hash = 'sha512';
                }
                $key = $key->withHash($hash);
                break;
            case 'ssh-dss':
                $key = DSA::loadFormat('OpenSSH', $server_public_host_key)->withSignatureFormat('SSH2')->withHash('sha1');
                break;
            case 'ssh-rsa':
            case 'rsa-sha2-256':
            case 'rsa-sha2-512':
                list(, $signature) = Strings::unpackSSH2('ss', $signature);
                $key = RSA::loadFormat('OpenSSH', $server_public_host_key)->withPadding(RSA::SIGNATURE_PKCS1);
                switch ($this->signature_format) {
                    case 'rsa-sha2-512':
                        $hash = 'sha512';
                        break;
                    case 'rsa-sha2-256':
                        $hash = 'sha256';
                        break;
                    default:
                        $hash = 'sha1';
                }
                $key = $key->withHash($hash);
                break;
            default:
                $this->disconnect_helper(NET_SSH2_DISCONNECT_HOST_KEY_NOT_VERIFIABLE);
                throw new NoSupportedAlgorithmsException('Unsupported signature format');
        }
        if (!$key->verify($this->exchange_hash, $signature)) {
            return $this->disconnect_helper(NET_SSH2_DISCONNECT_HOST_KEY_NOT_VERIFIABLE);
        }
        return $this->signature_format . ' ' . $server_public_host_key;
    }
    public function getExitStatus()
    {
        if (is_null($this->exit_status)) {
            return \false;
        }
        return $this->exit_status;
    }
    public function getWindowColumns()
    {
        return $this->windowColumns;
    }
    public function getWindowRows()
    {
        return $this->windowRows;
    }
    public function setWindowColumns($value)
    {
        $this->windowColumns = $value;
    }
    public function setWindowRows($value)
    {
        $this->windowRows = $value;
    }
    public function setWindowSize($columns = 80, $rows = 24)
    {
        $this->windowColumns = $columns;
        $this->windowRows = $rows;
    }
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return $this->getResourceId();
    }
    public function getResourceId()
    {
        return '{' . spl_object_hash($this) . '}';
    }
    public static function getConnectionByResourceId($id)
    {
        if (isset(self::$connections[$id])) {
            return (self::$connections[$id] instanceof WeakReference) ? self::$connections[$id]->get() : self::$connections[$id];
        }
        return \false;
    }
    public static function getConnections()
    {
        if (!class_exists('WeakReference')) {
            return self::$connections;
        }
        $temp = [];
        foreach (self::$connections as $key => $ref) {
            $temp[$key] = $ref->get();
        }
        return $temp;
    }
    private function updateLogHistory($old, $new)
    {
        if (defined('Staatic\Vendor\NET_SSH2_LOGGING') && NET_SSH2_LOGGING == self::LOG_COMPLEX) {
            $this->message_number_log[count($this->message_number_log) - 1] = str_replace($old, $new, $this->message_number_log[count($this->message_number_log) - 1]);
        }
    }
    public function getAuthMethodsToContinue()
    {
        return $this->auth_methods_to_continue;
    }
    public function enableSmartMFA()
    {
        $this->smartMFA = \true;
    }
    public function disableSmartMFA()
    {
        $this->smartMFA = \false;
    }
}
