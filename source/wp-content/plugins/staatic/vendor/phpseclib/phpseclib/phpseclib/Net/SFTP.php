<?php

namespace Staatic\Vendor\phpseclib3\Net;

use UnexpectedValueException;
use BadFunctionCallException;
use Exception;
use RuntimeException;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_REQUEST;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_DATA;
use const Staatic\Vendor\NET_SFTP_INIT;
use const Staatic\Vendor\NET_SFTP_VERSION;
use const Staatic\Vendor\NET_SFTP_EXTENDED;
use const Staatic\Vendor\NET_SFTP_STATUS;
use const Staatic\Vendor\NET_SFTP_STATUS_OK;
use const Staatic\Vendor\NET_SFTP_REALPATH;
use const Staatic\Vendor\NET_SFTP_NAME;
use const Staatic\Vendor\NET_SFTP_OPENDIR;
use const Staatic\Vendor\NET_SFTP_HANDLE;
use const Staatic\Vendor\NET_SFTP_TYPE_DIRECTORY;
use const Staatic\Vendor\NET_SFTP_READDIR;
use const Staatic\Vendor\NET_SFTP_STATUS_EOF;
use const Staatic\Vendor\NET_SFTP_STAT;
use const Staatic\Vendor\NET_SFTP_TYPE_REGULAR;
use const Staatic\Vendor\NET_SFTP_LSTAT;
use const Staatic\Vendor\NET_SFTP_TYPE_SYMLINK;
use const Staatic\Vendor\NET_SFTP_ATTRS;
use const Staatic\Vendor\NET_SFTP_ATTR_SIZE;
use const Staatic\Vendor\NET_SFTP_ATTR_ACCESSTIME;
use const Staatic\Vendor\NET_SFTP_ATTR_MODIFYTIME;
use const Staatic\Vendor\NET_SFTP_OPEN_OPEN_EXISTING;
use const Staatic\Vendor\NET_SFTP_OPEN_WRITE;
use const Staatic\Vendor\NET_SFTP_OPEN_CREATE;
use const Staatic\Vendor\NET_SFTP_OPEN_EXCL;
use const Staatic\Vendor\NET_SFTP_OPEN;
use const Staatic\Vendor\NET_SFTP_ATTR_UIDGID;
use const Staatic\Vendor\NET_SFTP_ATTR_OWNERGROUP;
use const Staatic\Vendor\NET_SFTP_ATTR_PERMISSIONS;
use const Staatic\Vendor\NET_SFTP_TYPE_UNKNOWN;
use const Staatic\Vendor\NET_SFTP_SETSTAT;
use const Staatic\Vendor\NET_SFTP_QUEUE_SIZE;
use const Staatic\Vendor\NET_SFTP_READLINK;
use const Staatic\Vendor\NET_SFTP_LINK;
use const Staatic\Vendor\NET_SFTP_SYMLINK;
use const Staatic\Vendor\NET_SFTP_MKDIR;
use const Staatic\Vendor\NET_SFTP_RMDIR;
use const Staatic\Vendor\NET_SFTP_OPEN_OPEN_OR_CREATE;
use const Staatic\Vendor\NET_SFTP_OPEN_CREATE_TRUNCATE;
use const Staatic\Vendor\NET_SFTP_OPEN_TRUNCATE;
use const Staatic\Vendor\NET_SFTP_WRITE;
use const Staatic\Vendor\NET_SFTP_UPLOAD_QUEUE_SIZE;
use const Staatic\Vendor\NET_SFTP_CLOSE;
use const Staatic\Vendor\NET_SFTP_OPEN_READ;
use const Staatic\Vendor\NET_SFTP_READ;
use const Staatic\Vendor\NET_SFTP_DATA;
use const Staatic\Vendor\NET_SFTP_REMOVE;
use const Staatic\Vendor\NET_SFTP_STATUS_NO_SUCH_FILE;
use const Staatic\Vendor\NET_SFTP_TYPE_BLOCK_DEVICE;
use const Staatic\Vendor\NET_SFTP_TYPE_CHAR_DEVICE;
use const Staatic\Vendor\NET_SFTP_TYPE_FIFO;
use const Staatic\Vendor\NET_SFTP_RENAME;
use const Staatic\Vendor\NET_SFTP_ATTR_SUBSECOND_TIMES;
use const Staatic\Vendor\NET_SFTP_ATTR_CREATETIME;
use const Staatic\Vendor\NET_SFTP_ATTR_ACL;
use const Staatic\Vendor\NET_SFTP_ATTR_BITS;
use const Staatic\Vendor\NET_SFTP_ATTR_ALLOCATION_SIZE;
use const Staatic\Vendor\NET_SFTP_ATTR_TEXT_HINT;
use const Staatic\Vendor\NET_SFTP_ATTR_MIME_TYPE;
use const Staatic\Vendor\NET_SFTP_ATTR_LINK_COUNT;
use const Staatic\Vendor\NET_SFTP_ATTR_UNTRANSLATED_NAME;
use const Staatic\Vendor\NET_SFTP_ATTR_CTIME;
use const Staatic\Vendor\NET_SFTP_ATTR_EXTENDED;
use const Staatic\Vendor\NET_SFTP_TYPE_SOCKET;
use const Staatic\Vendor\NET_SFTP_TYPE_SPECIAL;
use const Staatic\Vendor\NET_SSH2_MSG_CHANNEL_CLOSE;
use const Staatic\Vendor\NET_SFTP_LOGGING;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Exception\FileNotFoundException;
class SFTP extends SSH2
{
    const CHANNEL = 0x100;
    const SOURCE_LOCAL_FILE = 1;
    const SOURCE_STRING = 2;
    const SOURCE_CALLBACK = 16;
    const RESUME = 4;
    const RESUME_START = 8;
    private static $packet_types = [];
    private static $status_codes = [];
    private static $attributes;
    private static $open_flags;
    private static $open_flags5;
    private static $file_types;
    private $use_request_id = \false;
    private $packet_type = -1;
    private $packet_buffer = '';
    private $extensions = [];
    private $version;
    private $defaultVersion;
    private $preferredVersion = 3;
    private $pwd = \false;
    private $packet_type_log = [];
    private $packet_log = [];
    private $realtime_log_file;
    private $realtime_log_size;
    private $realtime_log_wrap;
    private $log_size;
    private $sftp_errors = [];
    private $stat_cache = [];
    private $max_sftp_packet;
    private $use_stat_cache = \true;
    protected $sortOptions = [];
    private $canonicalize_paths = \true;
    private $requestBuffer = [];
    private $preserveTime = \false;
    private $allow_arbitrary_length_packets = \false;
    private $channel_close = \false;
    private $partial_init = \false;
    public function __construct($host, $port = 22, $timeout = 10)
    {
        parent::__construct($host, $port, $timeout);
        $this->max_sftp_packet = 1 << 15;
        if (empty(self::$packet_types)) {
            self::$packet_types = [1 => 'Staatic\Vendor\NET_SFTP_INIT', 2 => 'Staatic\Vendor\NET_SFTP_VERSION', 3 => 'Staatic\Vendor\NET_SFTP_OPEN', 4 => 'Staatic\Vendor\NET_SFTP_CLOSE', 5 => 'Staatic\Vendor\NET_SFTP_READ', 6 => 'Staatic\Vendor\NET_SFTP_WRITE', 7 => 'Staatic\Vendor\NET_SFTP_LSTAT', 9 => 'Staatic\Vendor\NET_SFTP_SETSTAT', 10 => 'Staatic\Vendor\NET_SFTP_FSETSTAT', 11 => 'Staatic\Vendor\NET_SFTP_OPENDIR', 12 => 'Staatic\Vendor\NET_SFTP_READDIR', 13 => 'Staatic\Vendor\NET_SFTP_REMOVE', 14 => 'Staatic\Vendor\NET_SFTP_MKDIR', 15 => 'Staatic\Vendor\NET_SFTP_RMDIR', 16 => 'Staatic\Vendor\NET_SFTP_REALPATH', 17 => 'Staatic\Vendor\NET_SFTP_STAT', 18 => 'Staatic\Vendor\NET_SFTP_RENAME', 19 => 'Staatic\Vendor\NET_SFTP_READLINK', 20 => 'Staatic\Vendor\NET_SFTP_SYMLINK', 21 => 'Staatic\Vendor\NET_SFTP_LINK', 101 => 'Staatic\Vendor\NET_SFTP_STATUS', 102 => 'Staatic\Vendor\NET_SFTP_HANDLE', 103 => 'Staatic\Vendor\NET_SFTP_DATA', 104 => 'Staatic\Vendor\NET_SFTP_NAME', 105 => 'Staatic\Vendor\NET_SFTP_ATTRS', 200 => 'Staatic\Vendor\NET_SFTP_EXTENDED'];
            self::$status_codes = [0 => 'Staatic\Vendor\NET_SFTP_STATUS_OK', 1 => 'Staatic\Vendor\NET_SFTP_STATUS_EOF', 2 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_SUCH_FILE', 3 => 'Staatic\Vendor\NET_SFTP_STATUS_PERMISSION_DENIED', 4 => 'Staatic\Vendor\NET_SFTP_STATUS_FAILURE', 5 => 'Staatic\Vendor\NET_SFTP_STATUS_BAD_MESSAGE', 6 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_CONNECTION', 7 => 'Staatic\Vendor\NET_SFTP_STATUS_CONNECTION_LOST', 8 => 'Staatic\Vendor\NET_SFTP_STATUS_OP_UNSUPPORTED', 9 => 'Staatic\Vendor\NET_SFTP_STATUS_INVALID_HANDLE', 10 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_SUCH_PATH', 11 => 'Staatic\Vendor\NET_SFTP_STATUS_FILE_ALREADY_EXISTS', 12 => 'Staatic\Vendor\NET_SFTP_STATUS_WRITE_PROTECT', 13 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_MEDIA', 14 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_SPACE_ON_FILESYSTEM', 15 => 'Staatic\Vendor\NET_SFTP_STATUS_QUOTA_EXCEEDED', 16 => 'Staatic\Vendor\NET_SFTP_STATUS_UNKNOWN_PRINCIPAL', 17 => 'Staatic\Vendor\NET_SFTP_STATUS_LOCK_CONFLICT', 18 => 'Staatic\Vendor\NET_SFTP_STATUS_DIR_NOT_EMPTY', 19 => 'Staatic\Vendor\NET_SFTP_STATUS_NOT_A_DIRECTORY', 20 => 'Staatic\Vendor\NET_SFTP_STATUS_INVALID_FILENAME', 21 => 'Staatic\Vendor\NET_SFTP_STATUS_LINK_LOOP', 22 => 'Staatic\Vendor\NET_SFTP_STATUS_CANNOT_DELETE', 23 => 'Staatic\Vendor\NET_SFTP_STATUS_INVALID_PARAMETER', 24 => 'Staatic\Vendor\NET_SFTP_STATUS_FILE_IS_A_DIRECTORY', 25 => 'Staatic\Vendor\NET_SFTP_STATUS_BYTE_RANGE_LOCK_CONFLICT', 26 => 'Staatic\Vendor\NET_SFTP_STATUS_BYTE_RANGE_LOCK_REFUSED', 27 => 'Staatic\Vendor\NET_SFTP_STATUS_DELETE_PENDING', 28 => 'Staatic\Vendor\NET_SFTP_STATUS_FILE_CORRUPT', 29 => 'Staatic\Vendor\NET_SFTP_STATUS_OWNER_INVALID', 30 => 'Staatic\Vendor\NET_SFTP_STATUS_GROUP_INVALID', 31 => 'Staatic\Vendor\NET_SFTP_STATUS_NO_MATCHING_BYTE_RANGE_LOCK'];
            self::$attributes = [0x1 => 'Staatic\Vendor\NET_SFTP_ATTR_SIZE', 0x2 => 'Staatic\Vendor\NET_SFTP_ATTR_UIDGID', 0x80 => 'Staatic\Vendor\NET_SFTP_ATTR_OWNERGROUP', 0x4 => 'Staatic\Vendor\NET_SFTP_ATTR_PERMISSIONS', 0x8 => 'Staatic\Vendor\NET_SFTP_ATTR_ACCESSTIME', 0x10 => 'Staatic\Vendor\NET_SFTP_ATTR_CREATETIME', 0x20 => 'Staatic\Vendor\NET_SFTP_ATTR_MODIFYTIME', 0x40 => 'Staatic\Vendor\NET_SFTP_ATTR_ACL', 0x100 => 'Staatic\Vendor\NET_SFTP_ATTR_SUBSECOND_TIMES', 0x200 => 'Staatic\Vendor\NET_SFTP_ATTR_BITS', 0x400 => 'Staatic\Vendor\NET_SFTP_ATTR_ALLOCATION_SIZE', 0x800 => 'Staatic\Vendor\NET_SFTP_ATTR_TEXT_HINT', 0x1000 => 'Staatic\Vendor\NET_SFTP_ATTR_MIME_TYPE', 0x2000 => 'Staatic\Vendor\NET_SFTP_ATTR_LINK_COUNT', 0x4000 => 'Staatic\Vendor\NET_SFTP_ATTR_UNTRANSLATED_NAME', 0x8000 => 'Staatic\Vendor\NET_SFTP_ATTR_CTIME', (\PHP_INT_SIZE == 4) ? -1 << 31 : 0x80000000 => 'Staatic\Vendor\NET_SFTP_ATTR_EXTENDED'];
            self::$open_flags = [0x1 => 'Staatic\Vendor\NET_SFTP_OPEN_READ', 0x2 => 'Staatic\Vendor\NET_SFTP_OPEN_WRITE', 0x4 => 'Staatic\Vendor\NET_SFTP_OPEN_APPEND', 0x8 => 'Staatic\Vendor\NET_SFTP_OPEN_CREATE', 0x10 => 'Staatic\Vendor\NET_SFTP_OPEN_TRUNCATE', 0x20 => 'Staatic\Vendor\NET_SFTP_OPEN_EXCL', 0x40 => 'Staatic\Vendor\NET_SFTP_OPEN_TEXT'];
            self::$open_flags5 = [0x0 => 'Staatic\Vendor\NET_SFTP_OPEN_CREATE_NEW', 0x1 => 'Staatic\Vendor\NET_SFTP_OPEN_CREATE_TRUNCATE', 0x2 => 'Staatic\Vendor\NET_SFTP_OPEN_OPEN_EXISTING', 0x3 => 'Staatic\Vendor\NET_SFTP_OPEN_OPEN_OR_CREATE', 0x4 => 'Staatic\Vendor\NET_SFTP_OPEN_TRUNCATE_EXISTING', 0x8 => 'Staatic\Vendor\NET_SFTP_OPEN_APPEND_DATA', 0x10 => 'Staatic\Vendor\NET_SFTP_OPEN_APPEND_DATA_ATOMIC', 0x20 => 'Staatic\Vendor\NET_SFTP_OPEN_TEXT_MODE', 0x40 => 'Staatic\Vendor\NET_SFTP_OPEN_BLOCK_READ', 0x80 => 'Staatic\Vendor\NET_SFTP_OPEN_BLOCK_WRITE', 0x100 => 'Staatic\Vendor\NET_SFTP_OPEN_BLOCK_DELETE', 0x200 => 'Staatic\Vendor\NET_SFTP_OPEN_BLOCK_ADVISORY', 0x400 => 'Staatic\Vendor\NET_SFTP_OPEN_NOFOLLOW', 0x800 => 'Staatic\Vendor\NET_SFTP_OPEN_DELETE_ON_CLOSE', 0x1000 => 'Staatic\Vendor\NET_SFTP_OPEN_ACCESS_AUDIT_ALARM_INFO', 0x2000 => 'Staatic\Vendor\NET_SFTP_OPEN_ACCESS_BACKUP', 0x4000 => 'Staatic\Vendor\NET_SFTP_OPEN_BACKUP_STREAM', 0x8000 => 'Staatic\Vendor\NET_SFTP_OPEN_OVERRIDE_OWNER'];
            self::$file_types = [1 => 'Staatic\Vendor\NET_SFTP_TYPE_REGULAR', 2 => 'Staatic\Vendor\NET_SFTP_TYPE_DIRECTORY', 3 => 'Staatic\Vendor\NET_SFTP_TYPE_SYMLINK', 4 => 'Staatic\Vendor\NET_SFTP_TYPE_SPECIAL', 5 => 'Staatic\Vendor\NET_SFTP_TYPE_UNKNOWN', 6 => 'Staatic\Vendor\NET_SFTP_TYPE_SOCKET', 7 => 'Staatic\Vendor\NET_SFTP_TYPE_CHAR_DEVICE', 8 => 'Staatic\Vendor\NET_SFTP_TYPE_BLOCK_DEVICE', 9 => 'Staatic\Vendor\NET_SFTP_TYPE_FIFO'];
            self::define_array(self::$packet_types, self::$status_codes, self::$attributes, self::$open_flags, self::$open_flags5, self::$file_types);
        }
        if (!defined('Staatic\Vendor\NET_SFTP_QUEUE_SIZE')) {
            define('Staatic\Vendor\NET_SFTP_QUEUE_SIZE', 32);
        }
        if (!defined('Staatic\Vendor\NET_SFTP_UPLOAD_QUEUE_SIZE')) {
            define('Staatic\Vendor\NET_SFTP_UPLOAD_QUEUE_SIZE', 1024);
        }
    }
    private function precheck()
    {
        if (!($this->bitmap & SSH2::MASK_LOGIN)) {
            return \false;
        }
        if ($this->pwd === \false) {
            return $this->init_sftp_connection();
        }
        return \true;
    }
    private function partial_init_sftp_connection()
    {
        $response = $this->open_channel(self::CHANNEL, \true);
        if ($response === \true && $this->isTimeout()) {
            return \false;
        }
        $packet = Strings::packSSH2('CNsbs', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL], 'subsystem', \true, 'sftp');
        $this->send_binary_packet($packet);
        $this->channel_status[self::CHANNEL] = NET_SSH2_MSG_CHANNEL_REQUEST;
        $response = $this->get_channel_packet(self::CHANNEL, \true);
        if ($response === \false) {
            $command = "test -x /usr/lib/sftp-server && exec /usr/lib/sftp-server\n" . "test -x /usr/local/lib/sftp-server && exec /usr/local/lib/sftp-server\n" . "exec sftp-server";
            $packet = Strings::packSSH2('CNsCs', NET_SSH2_MSG_CHANNEL_REQUEST, $this->server_channels[self::CHANNEL], 'exec', 1, $command);
            $this->send_binary_packet($packet);
            $this->channel_status[self::CHANNEL] = NET_SSH2_MSG_CHANNEL_REQUEST;
            $response = $this->get_channel_packet(self::CHANNEL, \true);
            if ($response === \false) {
                return \false;
            }
        } elseif ($response === \true && $this->isTimeout()) {
            return \false;
        }
        $this->channel_status[self::CHANNEL] = NET_SSH2_MSG_CHANNEL_DATA;
        $this->send_sftp_packet(NET_SFTP_INIT, "\x00\x00\x00\x03");
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_VERSION) {
            throw new UnexpectedValueException('Expected NET_SFTP_VERSION. ' . 'Got packet type: ' . $this->packet_type);
        }
        $this->use_request_id = \true;
        list($this->defaultVersion) = Strings::unpackSSH2('N', $response);
        while (!empty($response)) {
            list($key, $value) = Strings::unpackSSH2('ss', $response);
            $this->extensions[$key] = $value;
        }
        $this->partial_init = \true;
        return \true;
    }
    private function init_sftp_connection()
    {
        if (!$this->partial_init && !$this->partial_init_sftp_connection()) {
            return \false;
        }
        $this->version = $this->defaultVersion;
        if (isset($this->extensions['versions']) && (!$this->preferredVersion || $this->preferredVersion != $this->version)) {
            $versions = explode(',', $this->extensions['versions']);
            $supported = [6, 5, 4];
            if ($this->preferredVersion) {
                $supported = array_diff($supported, [$this->preferredVersion]);
                array_unshift($supported, $this->preferredVersion);
            }
            foreach ($supported as $ver) {
                if (in_array($ver, $versions)) {
                    if ($ver === $this->version) {
                        break;
                    }
                    $this->version = (int) $ver;
                    $packet = Strings::packSSH2('ss', 'version-select', "{$ver}");
                    $this->send_sftp_packet(NET_SFTP_EXTENDED, $packet);
                    $response = $this->get_sftp_packet();
                    if ($this->packet_type != NET_SFTP_STATUS) {
                        throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
                    }
                    list($status) = Strings::unpackSSH2('N', $response);
                    if ($status != NET_SFTP_STATUS_OK) {
                        $this->logError($response, $status);
                        throw new UnexpectedValueException('Expected NET_SFTP_STATUS_OK. ' . ' Got ' . $status);
                    }
                    break;
                }
            }
        }
        if ($this->version < 2 || $this->version > 6) {
            return \false;
        }
        $this->pwd = \true;
        try {
            $this->pwd = $this->realpath('.');
        } catch (UnexpectedValueException $e) {
            if (!$this->canonicalize_paths) {
                throw $e;
            }
            $this->canonicalize_paths = \false;
            $this->reset_sftp();
            return $this->init_sftp_connection();
        }
        $this->update_stat_cache($this->pwd, []);
        return \true;
    }
    public function disableStatCache()
    {
        $this->use_stat_cache = \false;
    }
    public function enableStatCache()
    {
        $this->use_stat_cache = \true;
    }
    public function clearStatCache()
    {
        $this->stat_cache = [];
    }
    public function enablePathCanonicalization()
    {
        $this->canonicalize_paths = \true;
    }
    public function disablePathCanonicalization()
    {
        $this->canonicalize_paths = \false;
    }
    public function enableArbitraryLengthPackets()
    {
        $this->allow_arbitrary_length_packets = \true;
    }
    public function disableArbitraryLengthPackets()
    {
        $this->allow_arbitrary_length_packets = \false;
    }
    public function pwd()
    {
        if (!$this->precheck()) {
            return \false;
        }
        return $this->pwd;
    }
    private function logError($response, $status = -1)
    {
        if ($status == -1) {
            list($status) = Strings::unpackSSH2('N', $response);
        }
        $error = self::$status_codes[$status];
        if ($this->version > 2) {
            list($message) = Strings::unpackSSH2('s', $response);
            $this->sftp_errors[] = "{$error}: {$message}";
        } else {
            $this->sftp_errors[] = $error;
        }
    }
    public function realpath($path)
    {
        if ($this->precheck() === \false) {
            return \false;
        }
        if (!$this->canonicalize_paths) {
            if ($this->pwd === \true) {
                return '.';
            }
            if (!strlen($path) || $path[0] != '/') {
                $path = $this->pwd . '/' . $path;
            }
            $parts = explode('/', $path);
            $afterPWD = $beforePWD = [];
            foreach ($parts as $part) {
                switch ($part) {
                    case '.':
                        break;
                    case '..':
                        if (!empty($afterPWD)) {
                            array_pop($afterPWD);
                        } else {
                            $beforePWD[] = '..';
                        }
                        break;
                    default:
                        $afterPWD[] = $part;
                }
            }
            $beforePWD = count($beforePWD) ? implode('/', $beforePWD) : '.';
            return $beforePWD . '/' . implode('/', $afterPWD);
        }
        if ($this->pwd === \true) {
            $this->send_sftp_packet(NET_SFTP_REALPATH, Strings::packSSH2('s', $path));
            $response = $this->get_sftp_packet();
            switch ($this->packet_type) {
                case NET_SFTP_NAME:
                    list(, $filename) = Strings::unpackSSH2('Ns', $response);
                    return $filename;
                case NET_SFTP_STATUS:
                    $this->logError($response);
                    return \false;
                default:
                    throw new UnexpectedValueException('Expected NET_SFTP_NAME or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
            }
        }
        if (!strlen($path) || $path[0] != '/') {
            $path = $this->pwd . '/' . $path;
        }
        $path = explode('/', $path);
        $new = [];
        foreach ($path as $dir) {
            if (!strlen($dir)) {
                continue;
            }
            switch ($dir) {
                case '..':
                    array_pop($new);
                case '.':
                    break;
                default:
                    $new[] = $dir;
            }
        }
        return '/' . implode('/', $new);
    }
    public function chdir($dir)
    {
        if (!$this->precheck()) {
            return \false;
        }
        if ($dir === '') {
            $dir = './';
        } elseif ($dir[strlen($dir) - 1] != '/') {
            $dir .= '/';
        }
        $dir = $this->realpath($dir);
        if ($this->use_stat_cache && is_array($this->query_stat_cache($dir))) {
            $this->pwd = $dir;
            return \true;
        }
        $this->send_sftp_packet(NET_SFTP_OPENDIR, Strings::packSSH2('s', $dir));
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS' . 'Got packet type: ' . $this->packet_type);
        }
        if (!$this->close_handle($handle)) {
            return \false;
        }
        $this->update_stat_cache($dir, []);
        $this->pwd = $dir;
        return \true;
    }
    public function nlist($dir = '.', $recursive = \false)
    {
        return $this->nlist_helper($dir, $recursive, '');
    }
    private function nlist_helper($dir, $recursive, $relativeDir)
    {
        $files = $this->readlist($dir, \false);
        if (is_int($files)) {
            return \false;
        }
        if (!$recursive || $files === \false) {
            return $files;
        }
        $result = [];
        foreach ($files as $value) {
            if ($value == '.' || $value == '..') {
                $result[] = $relativeDir . $value;
                continue;
            }
            if (is_array($this->query_stat_cache($this->realpath($dir . '/' . $value)))) {
                $temp = $this->nlist_helper($dir . '/' . $value, \true, $relativeDir . $value . '/');
                $temp = is_array($temp) ? $temp : [];
                $result = array_merge($result, $temp);
            } else {
                $result[] = $relativeDir . $value;
            }
        }
        return $result;
    }
    public function rawlist($dir = '.', $recursive = \false)
    {
        $files = $this->readlist($dir, \true);
        if (is_int($files)) {
            return \false;
        }
        if (!$recursive || $files === \false) {
            return $files;
        }
        static $depth = 0;
        foreach ($files as $key => $value) {
            if ($depth != 0 && $key == '..') {
                unset($files[$key]);
                continue;
            }
            $is_directory = \false;
            if ($key != '.' && $key != '..') {
                if ($this->use_stat_cache) {
                    $is_directory = is_array($this->query_stat_cache($this->realpath($dir . '/' . $key)));
                } else {
                    $stat = $this->lstat($dir . '/' . $key);
                    $is_directory = $stat && $stat['type'] === NET_SFTP_TYPE_DIRECTORY;
                }
            }
            if ($is_directory) {
                $depth++;
                $files[$key] = $this->rawlist($dir . '/' . $key, \true);
                $depth--;
            } else {
                $files[$key] = (object) $value;
            }
        }
        return $files;
    }
    private function readlist($dir, $raw = \true)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $dir = $this->realpath($dir . '/');
        if ($dir === \false) {
            return \false;
        }
        $this->send_sftp_packet(NET_SFTP_OPENDIR, Strings::packSSH2('s', $dir));
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                list($status) = Strings::unpackSSH2('N', $response);
                $this->logError($response, $status);
                return $status;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        $this->update_stat_cache($dir, []);
        $contents = [];
        while (\true) {
            $this->send_sftp_packet(NET_SFTP_READDIR, Strings::packSSH2('s', $handle));
            $response = $this->get_sftp_packet();
            switch ($this->packet_type) {
                case NET_SFTP_NAME:
                    list($count) = Strings::unpackSSH2('N', $response);
                    for ($i = 0; $i < $count; $i++) {
                        list($shortname) = Strings::unpackSSH2('s', $response);
                        if ($this->version < 4) {
                            list($longname) = Strings::unpackSSH2('s', $response);
                        }
                        $attributes = $this->parseAttributes($response);
                        if (!isset($attributes['type']) && $this->version < 4) {
                            $fileType = $this->parseLongname($longname);
                            if ($fileType) {
                                $attributes['type'] = $fileType;
                            }
                        }
                        $contents[$shortname] = $attributes + ['filename' => $shortname];
                        if (isset($attributes['type']) && $attributes['type'] == NET_SFTP_TYPE_DIRECTORY && ($shortname != '.' && $shortname != '..')) {
                            $this->update_stat_cache($dir . '/' . $shortname, []);
                        } else {
                            if ($shortname == '..') {
                                $temp = $this->realpath($dir . '/..') . '/.';
                            } else {
                                $temp = $dir . '/' . $shortname;
                            }
                            $this->update_stat_cache($temp, (object) ['lstat' => $attributes]);
                        }
                    }
                    break;
                case NET_SFTP_STATUS:
                    list($status) = Strings::unpackSSH2('N', $response);
                    if ($status != NET_SFTP_STATUS_EOF) {
                        $this->logError($response, $status);
                        return $status;
                    }
                    break 2;
                default:
                    throw new UnexpectedValueException('Expected NET_SFTP_NAME or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
            }
        }
        if (!$this->close_handle($handle)) {
            return \false;
        }
        if (count($this->sortOptions)) {
            uasort($contents, [&$this, 'comparator']);
        }
        return $raw ? $contents : array_map('strval', array_keys($contents));
    }
    private function comparator(array $a, array $b)
    {
        switch (\true) {
            case $a['filename'] === '.' || $b['filename'] === '.':
                if ($a['filename'] === $b['filename']) {
                    return 0;
                }
                return ($a['filename'] === '.') ? -1 : 1;
            case $a['filename'] === '..' || $b['filename'] === '..':
                if ($a['filename'] === $b['filename']) {
                    return 0;
                }
                return ($a['filename'] === '..') ? -1 : 1;
            case isset($a['type']) && $a['type'] === NET_SFTP_TYPE_DIRECTORY:
                if (!isset($b['type'])) {
                    return 1;
                }
                if ($b['type'] !== $a['type']) {
                    return -1;
                }
                break;
            case isset($b['type']) && $b['type'] === NET_SFTP_TYPE_DIRECTORY:
                return 1;
        }
        foreach ($this->sortOptions as $sort => $order) {
            if (!isset($a[$sort]) || !isset($b[$sort])) {
                if (isset($a[$sort])) {
                    return -1;
                }
                if (isset($b[$sort])) {
                    return 1;
                }
                return 0;
            }
            switch ($sort) {
                case 'filename':
                    $result = strcasecmp($a['filename'], $b['filename']);
                    if ($result) {
                        return ($order === \SORT_DESC) ? -$result : $result;
                    }
                    break;
                case 'mode':
                    $a[$sort] &= 07777;
                    $b[$sort] &= 07777;
                default:
                    if ($a[$sort] === $b[$sort]) {
                        break;
                    }
                    return ($order === \SORT_ASC) ? $a[$sort] - $b[$sort] : ($b[$sort] - $a[$sort]);
            }
        }
    }
    public function setListOrder(...$args)
    {
        $this->sortOptions = [];
        if (empty($args)) {
            return;
        }
        $len = count($args) & 0x7ffffffe;
        for ($i = 0; $i < $len; $i += 2) {
            $this->sortOptions[$args[$i]] = $args[$i + 1];
        }
        if (!count($this->sortOptions)) {
            $this->sortOptions = ['bogus' => \true];
        }
    }
    private function update_stat_cache($path, $value)
    {
        if ($this->use_stat_cache === \false) {
            return;
        }
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));
        $temp =& $this->stat_cache;
        $max = count($dirs) - 1;
        foreach ($dirs as $i => $dir) {
            if (is_object($temp)) {
                $temp = [];
            }
            if (!isset($temp[$dir])) {
                $temp[$dir] = [];
            }
            if ($i === $max) {
                if (is_object($temp[$dir]) && is_object($value)) {
                    if (!isset($value->stat) && isset($temp[$dir]->stat)) {
                        $value->stat = $temp[$dir]->stat;
                    }
                    if (!isset($value->lstat) && isset($temp[$dir]->lstat)) {
                        $value->lstat = $temp[$dir]->lstat;
                    }
                }
                $temp[$dir] = $value;
                break;
            }
            $temp =& $temp[$dir];
        }
    }
    private function remove_from_stat_cache($path)
    {
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));
        $temp =& $this->stat_cache;
        $max = count($dirs) - 1;
        foreach ($dirs as $i => $dir) {
            if (!is_array($temp)) {
                return \false;
            }
            if ($i === $max) {
                unset($temp[$dir]);
                return \true;
            }
            if (!isset($temp[$dir])) {
                return \false;
            }
            $temp =& $temp[$dir];
        }
    }
    private function query_stat_cache($path)
    {
        $dirs = explode('/', preg_replace('#^/|/(?=/)|/$#', '', $path));
        $temp =& $this->stat_cache;
        foreach ($dirs as $dir) {
            if (!is_array($temp)) {
                return null;
            }
            if (!isset($temp[$dir])) {
                return null;
            }
            $temp =& $temp[$dir];
        }
        return $temp;
    }
    public function stat($filename)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $filename = $this->realpath($filename);
        if ($filename === \false) {
            return \false;
        }
        if ($this->use_stat_cache) {
            $result = $this->query_stat_cache($filename);
            if (is_array($result) && isset($result['.']) && isset($result['.']->stat)) {
                return $result['.']->stat;
            }
            if (is_object($result) && isset($result->stat)) {
                return $result->stat;
            }
        }
        $stat = $this->stat_helper($filename, NET_SFTP_STAT);
        if ($stat === \false) {
            $this->remove_from_stat_cache($filename);
            return \false;
        }
        if (isset($stat['type'])) {
            if ($stat['type'] == NET_SFTP_TYPE_DIRECTORY) {
                $filename .= '/.';
            }
            $this->update_stat_cache($filename, (object) ['stat' => $stat]);
            return $stat;
        }
        $pwd = $this->pwd;
        $stat['type'] = $this->chdir($filename) ? NET_SFTP_TYPE_DIRECTORY : NET_SFTP_TYPE_REGULAR;
        $this->pwd = $pwd;
        if ($stat['type'] == NET_SFTP_TYPE_DIRECTORY) {
            $filename .= '/.';
        }
        $this->update_stat_cache($filename, (object) ['stat' => $stat]);
        return $stat;
    }
    public function lstat($filename)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $filename = $this->realpath($filename);
        if ($filename === \false) {
            return \false;
        }
        if ($this->use_stat_cache) {
            $result = $this->query_stat_cache($filename);
            if (is_array($result) && isset($result['.']) && isset($result['.']->lstat)) {
                return $result['.']->lstat;
            }
            if (is_object($result) && isset($result->lstat)) {
                return $result->lstat;
            }
        }
        $lstat = $this->stat_helper($filename, NET_SFTP_LSTAT);
        if ($lstat === \false) {
            $this->remove_from_stat_cache($filename);
            return \false;
        }
        if (isset($lstat['type'])) {
            if ($lstat['type'] == NET_SFTP_TYPE_DIRECTORY) {
                $filename .= '/.';
            }
            $this->update_stat_cache($filename, (object) ['lstat' => $lstat]);
            return $lstat;
        }
        $stat = $this->stat_helper($filename, NET_SFTP_STAT);
        if ($lstat != $stat) {
            $lstat = array_merge($lstat, ['type' => NET_SFTP_TYPE_SYMLINK]);
            $this->update_stat_cache($filename, (object) ['lstat' => $lstat]);
            return $stat;
        }
        $pwd = $this->pwd;
        $lstat['type'] = $this->chdir($filename) ? NET_SFTP_TYPE_DIRECTORY : NET_SFTP_TYPE_REGULAR;
        $this->pwd = $pwd;
        if ($lstat['type'] == NET_SFTP_TYPE_DIRECTORY) {
            $filename .= '/.';
        }
        $this->update_stat_cache($filename, (object) ['lstat' => $lstat]);
        return $lstat;
    }
    private function stat_helper($filename, $type)
    {
        $packet = Strings::packSSH2('s', $filename);
        $this->send_sftp_packet($type, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_ATTRS:
                return $this->parseAttributes($response);
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
        }
        throw new UnexpectedValueException('Expected NET_SFTP_ATTRS or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
    }
    public function truncate($filename, $new_size)
    {
        $attr = Strings::packSSH2('NQ', NET_SFTP_ATTR_SIZE, $new_size);
        return $this->setstat($filename, $attr, \false);
    }
    public function touch($filename, $time = null, $atime = null)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $filename = $this->realpath($filename);
        if ($filename === \false) {
            return \false;
        }
        if (!isset($time)) {
            $time = time();
        }
        if (!isset($atime)) {
            $atime = $time;
        }
        $attr = ($this->version < 4) ? pack('N3', NET_SFTP_ATTR_ACCESSTIME, $atime, $time) : Strings::packSSH2('NQ2', NET_SFTP_ATTR_ACCESSTIME | NET_SFTP_ATTR_MODIFYTIME, $atime, $time);
        $packet = Strings::packSSH2('s', $filename);
        $packet .= ($this->version >= 5) ? pack('N2', 0, NET_SFTP_OPEN_OPEN_EXISTING) : pack('N', NET_SFTP_OPEN_WRITE | NET_SFTP_OPEN_CREATE | NET_SFTP_OPEN_EXCL);
        $packet .= $attr;
        $this->send_sftp_packet(NET_SFTP_OPEN, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                return $this->close_handle(substr($response, 4));
            case NET_SFTP_STATUS:
                $this->logError($response);
                break;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        return $this->setstat($filename, $attr, \false);
    }
    public function chown($filename, $uid, $recursive = \false)
    {
        $attr = ($this->version < 4) ? pack('N3', NET_SFTP_ATTR_UIDGID, $uid, -1) : Strings::packSSH2('Nss', NET_SFTP_ATTR_OWNERGROUP, $uid, '');
        return $this->setstat($filename, $attr, $recursive);
    }
    public function chgrp($filename, $gid, $recursive = \false)
    {
        $attr = ($this->version < 4) ? pack('N3', NET_SFTP_ATTR_UIDGID, -1, $gid) : Strings::packSSH2('Nss', NET_SFTP_ATTR_OWNERGROUP, '', $gid);
        return $this->setstat($filename, $attr, $recursive);
    }
    public function chmod($mode, $filename, $recursive = \false)
    {
        if (is_string($mode) && is_int($filename)) {
            $temp = $mode;
            $mode = $filename;
            $filename = $temp;
        }
        $attr = pack('N2', NET_SFTP_ATTR_PERMISSIONS, $mode & 07777);
        if (!$this->setstat($filename, $attr, $recursive)) {
            return \false;
        }
        if ($recursive) {
            return \true;
        }
        $filename = $this->realpath($filename);
        $packet = pack('Na*', strlen($filename), $filename);
        $this->send_sftp_packet(NET_SFTP_STAT, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_ATTRS:
                $attrs = $this->parseAttributes($response);
                return $attrs['mode'];
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
        }
        throw new UnexpectedValueException('Expected NET_SFTP_ATTRS or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
    }
    private function setstat($filename, $attr, $recursive)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $filename = $this->realpath($filename);
        if ($filename === \false) {
            return \false;
        }
        $this->remove_from_stat_cache($filename);
        if ($recursive) {
            $i = 0;
            $result = $this->setstat_recursive($filename, $attr, $i);
            $this->read_put_responses($i);
            return $result;
        }
        $packet = Strings::packSSH2('s', $filename);
        $packet .= ($this->version >= 4) ? pack('a*Ca*', substr($attr, 0, 4), NET_SFTP_TYPE_UNKNOWN, substr($attr, 4)) : $attr;
        $this->send_sftp_packet(NET_SFTP_SETSTAT, $packet);
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        return \true;
    }
    private function setstat_recursive($path, $attr, &$i)
    {
        if (!$this->read_put_responses($i)) {
            return \false;
        }
        $i = 0;
        $entries = $this->readlist($path, \true);
        if ($entries === \false || is_int($entries)) {
            return $this->setstat($path, $attr, \false);
        }
        if (empty($entries)) {
            return \false;
        }
        unset($entries['.'], $entries['..']);
        foreach ($entries as $filename => $props) {
            if (!isset($props['type'])) {
                return \false;
            }
            $temp = $path . '/' . $filename;
            if ($props['type'] == NET_SFTP_TYPE_DIRECTORY) {
                if (!$this->setstat_recursive($temp, $attr, $i)) {
                    return \false;
                }
            } else {
                $packet = Strings::packSSH2('s', $temp);
                $packet .= ($this->version >= 4) ? pack('Ca*', NET_SFTP_TYPE_UNKNOWN, $attr) : $attr;
                $this->send_sftp_packet(NET_SFTP_SETSTAT, $packet);
                $i++;
                if ($i >= NET_SFTP_QUEUE_SIZE) {
                    if (!$this->read_put_responses($i)) {
                        return \false;
                    }
                    $i = 0;
                }
            }
        }
        $packet = Strings::packSSH2('s', $path);
        $packet .= ($this->version >= 4) ? pack('Ca*', NET_SFTP_TYPE_UNKNOWN, $attr) : $attr;
        $this->send_sftp_packet(NET_SFTP_SETSTAT, $packet);
        $i++;
        if ($i >= NET_SFTP_QUEUE_SIZE) {
            if (!$this->read_put_responses($i)) {
                return \false;
            }
            $i = 0;
        }
        return \true;
    }
    public function readlink($link)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $link = $this->realpath($link);
        $this->send_sftp_packet(NET_SFTP_READLINK, Strings::packSSH2('s', $link));
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_NAME:
                break;
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_NAME or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($count) = Strings::unpackSSH2('N', $response);
        if (!$count) {
            return \false;
        }
        list($filename) = Strings::unpackSSH2('s', $response);
        return $filename;
    }
    public function symlink($target, $link)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $link = $this->realpath($link);
        if ($this->version == 6) {
            $type = NET_SFTP_LINK;
            $packet = Strings::packSSH2('ssC', $link, $target, 1);
        } else {
            $type = NET_SFTP_SYMLINK;
            $packet = (substr($this->server_identifier, 0, 15) == 'SSH-2.0-OpenSSH') ? Strings::packSSH2('ss', $target, $link) : Strings::packSSH2('ss', $link, $target);
        }
        $this->send_sftp_packet($type, $packet);
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        return \true;
    }
    public function mkdir($dir, $mode = -1, $recursive = \false)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $dir = $this->realpath($dir);
        if ($recursive) {
            $dirs = explode('/', preg_replace('#/(?=/)|/$#', '', $dir));
            if (empty($dirs[0])) {
                array_shift($dirs);
                $dirs[0] = '/' . $dirs[0];
            }
            for ($i = 0; $i < count($dirs); $i++) {
                $temp = array_slice($dirs, 0, $i + 1);
                $temp = implode('/', $temp);
                $result = $this->mkdir_helper($temp, $mode);
            }
            return $result;
        }
        return $this->mkdir_helper($dir, $mode);
    }
    private function mkdir_helper($dir, $mode)
    {
        $this->send_sftp_packet(NET_SFTP_MKDIR, Strings::packSSH2('s', $dir) . "\x00\x00\x00\x00");
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        if ($mode !== -1) {
            $this->chmod($mode, $dir);
        }
        return \true;
    }
    public function rmdir($dir)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $dir = $this->realpath($dir);
        if ($dir === \false) {
            return \false;
        }
        $this->send_sftp_packet(NET_SFTP_RMDIR, Strings::packSSH2('s', $dir));
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        $this->remove_from_stat_cache($dir);
        return \true;
    }
    public function put($remote_file, $data, $mode = self::SOURCE_STRING, $start = -1, $local_start = -1, $progressCallback = null)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $remote_file = $this->realpath($remote_file);
        if ($remote_file === \false) {
            return \false;
        }
        $this->remove_from_stat_cache($remote_file);
        if ($this->version >= 5) {
            $flags = NET_SFTP_OPEN_OPEN_OR_CREATE;
        } else {
            $flags = NET_SFTP_OPEN_WRITE | NET_SFTP_OPEN_CREATE;
        }
        if ($start >= 0) {
            $offset = $start;
        } elseif ($mode & (self::RESUME | self::RESUME_START)) {
            $stat = $this->stat($remote_file);
            $offset = ($stat !== \false && $stat['size']) ? $stat['size'] : 0;
        } else {
            $offset = 0;
            if ($this->version >= 5) {
                $flags = NET_SFTP_OPEN_CREATE_TRUNCATE;
            } else {
                $flags |= NET_SFTP_OPEN_TRUNCATE;
            }
        }
        $this->remove_from_stat_cache($remote_file);
        $packet = Strings::packSSH2('s', $remote_file);
        $packet .= ($this->version >= 5) ? pack('N3', 0, $flags, 0) : pack('N2', $flags, 0);
        $this->send_sftp_packet(NET_SFTP_OPEN, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        $dataCallback = \false;
        switch (\true) {
            case $mode & self::SOURCE_CALLBACK:
                if (!is_callable($data)) {
                    throw new BadFunctionCallException("\$data should be is_callable() if you specify SOURCE_CALLBACK flag");
                }
                $dataCallback = $data;
                break;
            case is_resource($data):
                $mode = $mode & ~self::SOURCE_LOCAL_FILE;
                $info = stream_get_meta_data($data);
                if (isset($info['wrapper_type']) && $info['wrapper_type'] == 'PHP' && $info['stream_type'] == 'Input') {
                    $fp = fopen('php://memory', 'w+');
                    stream_copy_to_stream($data, $fp);
                    rewind($fp);
                } else {
                    $fp = $data;
                }
                break;
            case $mode & self::SOURCE_LOCAL_FILE:
                if (!is_file($data)) {
                    throw new FileNotFoundException("{$data} is not a valid file");
                }
                $fp = @fopen($data, 'rb');
                if (!$fp) {
                    return \false;
                }
        }
        if (isset($fp)) {
            $stat = fstat($fp);
            $size = (!empty($stat)) ? $stat['size'] : 0;
            if ($local_start >= 0) {
                fseek($fp, $local_start);
                $size -= $local_start;
            } elseif ($mode & self::RESUME) {
                fseek($fp, $offset);
                $size -= $offset;
            }
        } elseif ($dataCallback) {
            $size = 0;
        } else {
            $size = strlen($data);
        }
        $sent = 0;
        $size = ($size < 0) ? ($size & 0x7fffffff) + 0x80000000 : $size;
        $sftp_packet_size = $this->max_sftp_packet;
        $sftp_packet_size -= strlen($handle) + 25;
        $i = $j = 0;
        while ($dataCallback || ($size === 0 || $sent < $size)) {
            if ($dataCallback) {
                $temp = $dataCallback($sftp_packet_size);
                if (is_null($temp)) {
                    break;
                }
            } else {
                $temp = isset($fp) ? fread($fp, $sftp_packet_size) : substr($data, $sent, $sftp_packet_size);
                if ($temp === \false || $temp === '') {
                    break;
                }
            }
            $subtemp = $offset + $sent;
            $packet = pack('Na*N3a*', strlen($handle), $handle, $subtemp / 4294967296, $subtemp, strlen($temp), $temp);
            try {
                $this->send_sftp_packet(NET_SFTP_WRITE, $packet, $j);
            } catch (Exception $e) {
                if ($mode & self::SOURCE_LOCAL_FILE) {
                    fclose($fp);
                }
                throw $e;
            }
            $sent += strlen($temp);
            if (is_callable($progressCallback)) {
                $progressCallback($sent);
            }
            $i++;
            $j++;
            if ($i == NET_SFTP_UPLOAD_QUEUE_SIZE) {
                if (!$this->read_put_responses($i)) {
                    $i = 0;
                    break;
                }
                $i = 0;
            }
        }
        $result = $this->close_handle($handle);
        if (!$this->read_put_responses($i)) {
            if ($mode & self::SOURCE_LOCAL_FILE) {
                fclose($fp);
            }
            $this->close_handle($handle);
            return \false;
        }
        if ($mode & SFTP::SOURCE_LOCAL_FILE) {
            if (isset($fp) && is_resource($fp)) {
                fclose($fp);
            }
            if ($this->preserveTime) {
                $stat = stat($data);
                $attr = ($this->version < 4) ? pack('N3', NET_SFTP_ATTR_ACCESSTIME, $stat['atime'], $stat['mtime']) : Strings::packSSH2('NQ2', NET_SFTP_ATTR_ACCESSTIME | NET_SFTP_ATTR_MODIFYTIME, $stat['atime'], $stat['mtime']);
                if (!$this->setstat($remote_file, $attr, \false)) {
                    throw new RuntimeException('Error setting file time');
                }
            }
        }
        return $result;
    }
    private function read_put_responses($i)
    {
        while ($i--) {
            $response = $this->get_sftp_packet();
            if ($this->packet_type != NET_SFTP_STATUS) {
                throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
            }
            list($status) = Strings::unpackSSH2('N', $response);
            if ($status != NET_SFTP_STATUS_OK) {
                $this->logError($response, $status);
                break;
            }
        }
        return $i < 0;
    }
    private function close_handle($handle)
    {
        $this->send_sftp_packet(NET_SFTP_CLOSE, pack('Na*', strlen($handle), $handle));
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        return \true;
    }
    public function get($remote_file, $local_file = \false, $offset = 0, $length = -1, $progressCallback = null)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $remote_file = $this->realpath($remote_file);
        if ($remote_file === \false) {
            return \false;
        }
        $packet = Strings::packSSH2('s', $remote_file);
        $packet .= ($this->version >= 5) ? pack('N3', 0, NET_SFTP_OPEN_OPEN_EXISTING, 0) : pack('N2', NET_SFTP_OPEN_READ, 0);
        $this->send_sftp_packet(NET_SFTP_OPEN, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                $handle = substr($response, 4);
                break;
            case NET_SFTP_STATUS:
                $this->logError($response);
                return \false;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        if (is_resource($local_file)) {
            $fp = $local_file;
            $stat = fstat($fp);
            $res_offset = $stat['size'];
        } else {
            $res_offset = 0;
            if ($local_file !== \false && !is_callable($local_file)) {
                $fp = fopen($local_file, 'wb');
                if (!$fp) {
                    return \false;
                }
            } else {
                $content = '';
            }
        }
        $fclose_check = $local_file !== \false && !is_callable($local_file) && !is_resource($local_file);
        $start = $offset;
        $read = 0;
        while (\true) {
            $i = 0;
            while ($i < NET_SFTP_QUEUE_SIZE && ($length < 0 || $read < $length)) {
                $tempoffset = $start + $read;
                $packet_size = ($length > 0) ? min($this->max_sftp_packet, $length - $read) : $this->max_sftp_packet;
                $packet = Strings::packSSH2('sN3', $handle, $tempoffset / 4294967296, $tempoffset, $packet_size);
                try {
                    $this->send_sftp_packet(NET_SFTP_READ, $packet, $i);
                } catch (Exception $e) {
                    if ($fclose_check) {
                        fclose($fp);
                    }
                    throw $e;
                }
                $packet = null;
                $read += $packet_size;
                $i++;
            }
            if (!$i) {
                break;
            }
            $packets_sent = $i - 1;
            $clear_responses = \false;
            while ($i > 0) {
                $i--;
                if ($clear_responses) {
                    $this->get_sftp_packet($packets_sent - $i);
                    continue;
                } else {
                    $response = $this->get_sftp_packet($packets_sent - $i);
                }
                switch ($this->packet_type) {
                    case NET_SFTP_DATA:
                        $temp = substr($response, 4);
                        $offset += strlen($temp);
                        if ($local_file === \false) {
                            $content .= $temp;
                        } elseif (is_callable($local_file)) {
                            $local_file($temp);
                        } else {
                            fputs($fp, $temp);
                        }
                        if (is_callable($progressCallback)) {
                            call_user_func($progressCallback, $offset);
                        }
                        $temp = null;
                        break;
                    case NET_SFTP_STATUS:
                        $this->logError($response);
                        $clear_responses = \true;
                        break;
                    default:
                        if ($fclose_check) {
                            fclose($fp);
                        }
                        if ($this->channel_close) {
                            $this->partial_init = \false;
                            $this->init_sftp_connection();
                            return \false;
                        } else {
                            throw new UnexpectedValueException('Expected NET_SFTP_DATA or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
                        }
                }
                $response = null;
            }
            if ($clear_responses) {
                break;
            }
        }
        if ($fclose_check) {
            fclose($fp);
            if ($this->preserveTime) {
                $stat = $this->stat($remote_file);
                touch($local_file, $stat['mtime'], $stat['atime']);
            }
        }
        if (!$this->close_handle($handle)) {
            return \false;
        }
        return isset($content) ? $content : \true;
    }
    public function delete($path, $recursive = \true)
    {
        if (!$this->precheck()) {
            return \false;
        }
        if (is_object($path)) {
            $path = (string) $path;
        }
        if (!is_string($path) || $path == '') {
            return \false;
        }
        $path = $this->realpath($path);
        if ($path === \false) {
            return \false;
        }
        $this->send_sftp_packet(NET_SFTP_REMOVE, pack('Na*', strlen($path), $path));
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            if (!$recursive) {
                return \false;
            }
            $i = 0;
            $result = $this->delete_recursive($path, $i);
            $this->read_put_responses($i);
            return $result;
        }
        $this->remove_from_stat_cache($path);
        return \true;
    }
    private function delete_recursive($path, &$i)
    {
        if (!$this->read_put_responses($i)) {
            return \false;
        }
        $i = 0;
        $entries = $this->readlist($path, \true);
        if ($entries === NET_SFTP_STATUS_NO_SUCH_FILE) {
            return \false;
        }
        if ($entries === \false || is_int($entries)) {
            $entries = [];
        }
        unset($entries['.'], $entries['..']);
        foreach ($entries as $filename => $props) {
            if (!isset($props['type'])) {
                return \false;
            }
            $temp = $path . '/' . $filename;
            if ($props['type'] == NET_SFTP_TYPE_DIRECTORY) {
                if (!$this->delete_recursive($temp, $i)) {
                    return \false;
                }
            } else {
                $this->send_sftp_packet(NET_SFTP_REMOVE, Strings::packSSH2('s', $temp));
                $this->remove_from_stat_cache($temp);
                $i++;
                if ($i >= NET_SFTP_QUEUE_SIZE) {
                    if (!$this->read_put_responses($i)) {
                        return \false;
                    }
                    $i = 0;
                }
            }
        }
        $this->send_sftp_packet(NET_SFTP_RMDIR, Strings::packSSH2('s', $path));
        $this->remove_from_stat_cache($path);
        $i++;
        if ($i >= NET_SFTP_QUEUE_SIZE) {
            if (!$this->read_put_responses($i)) {
                return \false;
            }
            $i = 0;
        }
        return \true;
    }
    public function file_exists($path)
    {
        if ($this->use_stat_cache) {
            if (!$this->precheck()) {
                return \false;
            }
            $path = $this->realpath($path);
            $result = $this->query_stat_cache($path);
            if (isset($result)) {
                return $result !== \false;
            }
        }
        return $this->stat($path) !== \false;
    }
    public function is_dir($path)
    {
        $result = $this->get_stat_cache_prop($path, 'type');
        if ($result === \false) {
            return \false;
        }
        return $result === NET_SFTP_TYPE_DIRECTORY;
    }
    public function is_file($path)
    {
        $result = $this->get_stat_cache_prop($path, 'type');
        if ($result === \false) {
            return \false;
        }
        return $result === NET_SFTP_TYPE_REGULAR;
    }
    public function is_link($path)
    {
        $result = $this->get_lstat_cache_prop($path, 'type');
        if ($result === \false) {
            return \false;
        }
        return $result === NET_SFTP_TYPE_SYMLINK;
    }
    public function is_readable($path)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $packet = Strings::packSSH2('sNN', $this->realpath($path), NET_SFTP_OPEN_READ, 0);
        $this->send_sftp_packet(NET_SFTP_OPEN, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                return \true;
            case NET_SFTP_STATUS:
                return \false;
            default:
                throw new UnexpectedValueException('Expected NET_SFTP_HANDLE or NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
    }
    public function is_writable($path)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $packet = Strings::packSSH2('sNN', $this->realpath($path), NET_SFTP_OPEN_WRITE, 0);
        $this->send_sftp_packet(NET_SFTP_OPEN, $packet);
        $response = $this->get_sftp_packet();
        switch ($this->packet_type) {
            case NET_SFTP_HANDLE:
                return \true;
            case NET_SFTP_STATUS:
                return \false;
            default:
                throw new UnexpectedValueException('Expected SSH_FXP_HANDLE or SSH_FXP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
    }
    public function is_writeable($path)
    {
        return $this->is_writable($path);
    }
    public function fileatime($path)
    {
        return $this->get_stat_cache_prop($path, 'atime');
    }
    public function filemtime($path)
    {
        return $this->get_stat_cache_prop($path, 'mtime');
    }
    public function fileperms($path)
    {
        return $this->get_stat_cache_prop($path, 'mode');
    }
    public function fileowner($path)
    {
        return $this->get_stat_cache_prop($path, 'uid');
    }
    public function filegroup($path)
    {
        return $this->get_stat_cache_prop($path, 'gid');
    }
    private static function recursiveFilesize(array $files)
    {
        $size = 0;
        foreach ($files as $name => $file) {
            if ($name == '.' || $name == '..') {
                continue;
            }
            $size += is_array($file) ? self::recursiveFilesize($file) : $file->size;
        }
        return $size;
    }
    public function filesize($path, $recursive = \false)
    {
        return (!$recursive || $this->filetype($path) != 'dir') ? $this->get_stat_cache_prop($path, 'size') : self::recursiveFilesize($this->rawlist($path, \true));
    }
    public function filetype($path)
    {
        $type = $this->get_stat_cache_prop($path, 'type');
        if ($type === \false) {
            return \false;
        }
        switch ($type) {
            case NET_SFTP_TYPE_BLOCK_DEVICE:
                return 'block';
            case NET_SFTP_TYPE_CHAR_DEVICE:
                return 'char';
            case NET_SFTP_TYPE_DIRECTORY:
                return 'dir';
            case NET_SFTP_TYPE_FIFO:
                return 'fifo';
            case NET_SFTP_TYPE_REGULAR:
                return 'file';
            case NET_SFTP_TYPE_SYMLINK:
                return 'link';
            default:
                return \false;
        }
    }
    private function get_stat_cache_prop($path, $prop)
    {
        return $this->get_xstat_cache_prop($path, $prop, 'stat');
    }
    private function get_lstat_cache_prop($path, $prop)
    {
        return $this->get_xstat_cache_prop($path, $prop, 'lstat');
    }
    private function get_xstat_cache_prop($path, $prop, $type)
    {
        if (!$this->precheck()) {
            return \false;
        }
        if ($this->use_stat_cache) {
            $path = $this->realpath($path);
            $result = $this->query_stat_cache($path);
            if (is_object($result) && isset($result->{$type})) {
                return $result->{$type}[$prop];
            }
        }
        $result = $this->{$type}($path);
        if ($result === \false || !isset($result[$prop])) {
            return \false;
        }
        return $result[$prop];
    }
    public function rename($oldname, $newname)
    {
        if (!$this->precheck()) {
            return \false;
        }
        $oldname = $this->realpath($oldname);
        $newname = $this->realpath($newname);
        if ($oldname === \false || $newname === \false) {
            return \false;
        }
        $packet = Strings::packSSH2('ss', $oldname, $newname);
        if ($this->version >= 5) {
            $packet .= "\x00\x00\x00\x00";
        }
        $this->send_sftp_packet(NET_SFTP_RENAME, $packet);
        $response = $this->get_sftp_packet();
        if ($this->packet_type != NET_SFTP_STATUS) {
            throw new UnexpectedValueException('Expected NET_SFTP_STATUS. ' . 'Got packet type: ' . $this->packet_type);
        }
        list($status) = Strings::unpackSSH2('N', $response);
        if ($status != NET_SFTP_STATUS_OK) {
            $this->logError($response, $status);
            return \false;
        }
        $this->remove_from_stat_cache($oldname);
        $this->remove_from_stat_cache($newname);
        return \true;
    }
    private function parseTime($key, $flags, &$response)
    {
        $attr = [];
        list($attr[$key]) = Strings::unpackSSH2('Q', $response);
        if ($flags & NET_SFTP_ATTR_SUBSECOND_TIMES) {
            list($attr[$key . '-nseconds']) = Strings::unpackSSH2('N', $response);
        }
        return $attr;
    }
    protected function parseAttributes(&$response)
    {
        if ($this->version >= 4) {
            list($flags, $attr['type']) = Strings::unpackSSH2('NC', $response);
        } else {
            list($flags) = Strings::unpackSSH2('N', $response);
        }
        foreach (self::$attributes as $key => $value) {
            switch ($flags & $key) {
                case NET_SFTP_ATTR_UIDGID:
                    if ($this->version > 3) {
                        continue 2;
                    }
                    break;
                case NET_SFTP_ATTR_CREATETIME:
                case NET_SFTP_ATTR_MODIFYTIME:
                case NET_SFTP_ATTR_ACL:
                case NET_SFTP_ATTR_OWNERGROUP:
                case NET_SFTP_ATTR_SUBSECOND_TIMES:
                    if ($this->version < 4) {
                        continue 2;
                    }
                    break;
                case NET_SFTP_ATTR_BITS:
                    if ($this->version < 5) {
                        continue 2;
                    }
                    break;
                case NET_SFTP_ATTR_ALLOCATION_SIZE:
                case NET_SFTP_ATTR_TEXT_HINT:
                case NET_SFTP_ATTR_MIME_TYPE:
                case NET_SFTP_ATTR_LINK_COUNT:
                case NET_SFTP_ATTR_UNTRANSLATED_NAME:
                case NET_SFTP_ATTR_CTIME:
                    if ($this->version < 6) {
                        continue 2;
                    }
            }
            switch ($flags & $key) {
                case NET_SFTP_ATTR_SIZE:
                    list($attr['size']) = Strings::unpackSSH2('Q', $response);
                    break;
                case NET_SFTP_ATTR_UIDGID:
                    list($attr['uid'], $attr['gid']) = Strings::unpackSSH2('NN', $response);
                    break;
                case NET_SFTP_ATTR_PERMISSIONS:
                    list($attr['mode']) = Strings::unpackSSH2('N', $response);
                    $fileType = $this->parseMode($attr['mode']);
                    if ($this->version < 4 && $fileType !== \false) {
                        $attr += ['type' => $fileType];
                    }
                    break;
                case NET_SFTP_ATTR_ACCESSTIME:
                    if ($this->version >= 4) {
                        $attr += $this->parseTime('atime', $flags, $response);
                        break;
                    }
                    list($attr['atime'], $attr['mtime']) = Strings::unpackSSH2('NN', $response);
                    break;
                case NET_SFTP_ATTR_CREATETIME:
                    $attr += $this->parseTime('createtime', $flags, $response);
                    break;
                case NET_SFTP_ATTR_MODIFYTIME:
                    $attr += $this->parseTime('mtime', $flags, $response);
                    break;
                case NET_SFTP_ATTR_ACL:
                    list($count) = Strings::unpackSSH2('N', $response);
                    for ($i = 0; $i < $count; $i++) {
                        list($type, $flag, $mask, $who) = Strings::unpackSSH2('N3s', $result);
                    }
                    break;
                case NET_SFTP_ATTR_OWNERGROUP:
                    list($attr['owner'], $attr['$group']) = Strings::unpackSSH2('ss', $response);
                    break;
                case NET_SFTP_ATTR_SUBSECOND_TIMES:
                    break;
                case NET_SFTP_ATTR_BITS:
                    list($attrib_bits, $attrib_bits_valid) = Strings::unpackSSH2('N2', $response);
                    break;
                case NET_SFTP_ATTR_ALLOCATION_SIZE:
                    list($attr['allocation-size']) = Strings::unpackSSH2('Q', $response);
                    break;
                case NET_SFTP_ATTR_TEXT_HINT:
                    list($text_hint) = Strings::unpackSSH2('C', $response);
                    break;
                case NET_SFTP_ATTR_MIME_TYPE:
                    list($attr['mime-type']) = Strings::unpackSSH2('s', $response);
                    break;
                case NET_SFTP_ATTR_LINK_COUNT:
                    list($attr['link-count']) = Strings::unpackSSH2('N', $response);
                    break;
                case NET_SFTP_ATTR_UNTRANSLATED_NAME:
                    list($attr['untranslated-name']) = Strings::unpackSSH2('s', $response);
                    break;
                case NET_SFTP_ATTR_CTIME:
                    $attr += $this->parseTime('ctime', $flags, $response);
                    break;
                case NET_SFTP_ATTR_EXTENDED:
                    list($count) = Strings::unpackSSH2('N', $response);
                    for ($i = 0; $i < $count; $i++) {
                        list($key, $value) = Strings::unpackSSH2('ss', $response);
                        $attr[$key] = $value;
                    }
            }
        }
        return $attr;
    }
    private function parseMode($mode)
    {
        switch ($mode & 0170000) {
            case 00:
                return \false;
            case 040000:
                return NET_SFTP_TYPE_DIRECTORY;
            case 0100000:
                return NET_SFTP_TYPE_REGULAR;
            case 0120000:
                return NET_SFTP_TYPE_SYMLINK;
            case 010000:
                return NET_SFTP_TYPE_FIFO;
            case 020000:
                return NET_SFTP_TYPE_CHAR_DEVICE;
            case 060000:
                return NET_SFTP_TYPE_BLOCK_DEVICE;
            case 0140000:
                return NET_SFTP_TYPE_SOCKET;
            case 0160000:
                return NET_SFTP_TYPE_SPECIAL;
            default:
                return NET_SFTP_TYPE_UNKNOWN;
        }
    }
    private function parseLongname($longname)
    {
        if (preg_match('#^[^/]([r-][w-][xstST-]){3}#', $longname)) {
            switch ($longname[0]) {
                case '-':
                    return NET_SFTP_TYPE_REGULAR;
                case 'd':
                    return NET_SFTP_TYPE_DIRECTORY;
                case 'l':
                    return NET_SFTP_TYPE_SYMLINK;
                default:
                    return NET_SFTP_TYPE_SPECIAL;
            }
        }
        return \false;
    }
    private function send_sftp_packet($type, $data, $request_id = 1)
    {
        $this->curTimeout = $this->timeout;
        $packet = $this->use_request_id ? pack('NCNa*', strlen($data) + 5, $type, $request_id, $data) : pack('NCa*', strlen($data) + 1, $type, $data);
        $start = microtime(\true);
        $this->send_channel_packet(self::CHANNEL, $packet);
        $stop = microtime(\true);
        if (defined('Staatic\Vendor\NET_SFTP_LOGGING')) {
            $packet_type = '-> ' . self::$packet_types[$type] . ' (' . round($stop - $start, 4) . 's)';
            $this->append_log($packet_type, $data);
        }
    }
    private function reset_sftp()
    {
        $this->use_request_id = \false;
        $this->pwd = \false;
        $this->requestBuffer = [];
        $this->partial_init = \false;
    }
    protected function reset_connection()
    {
        parent::reset_connection();
        $this->reset_sftp();
    }
    private function get_sftp_packet($request_id = null)
    {
        $this->channel_close = \false;
        if (isset($request_id) && isset($this->requestBuffer[$request_id])) {
            $this->packet_type = $this->requestBuffer[$request_id]['packet_type'];
            $temp = $this->requestBuffer[$request_id]['packet'];
            unset($this->requestBuffer[$request_id]);
            return $temp;
        }
        $this->curTimeout = $this->timeout;
        $start = microtime(\true);
        while (strlen($this->packet_buffer) < 4) {
            $temp = $this->get_channel_packet(self::CHANNEL, \true);
            if ($temp === \true) {
                if ($this->channel_status[self::CHANNEL] === NET_SSH2_MSG_CHANNEL_CLOSE) {
                    $this->channel_close = \true;
                }
                $this->packet_type = \false;
                $this->packet_buffer = '';
                return \false;
            }
            $this->packet_buffer .= $temp;
        }
        if (strlen($this->packet_buffer) < 4) {
            throw new RuntimeException('Packet is too small');
        }
        extract(unpack('Nlength', Strings::shift($this->packet_buffer, 4)));
        $tempLength = $length;
        $tempLength -= strlen($this->packet_buffer);
        if (!$this->allow_arbitrary_length_packets && !$this->use_request_id && $tempLength > 256 * 1024) {
            throw new RuntimeException('Invalid Size');
        }
        while ($tempLength > 0) {
            $temp = $this->get_channel_packet(self::CHANNEL, \true);
            if ($temp === \true) {
                if ($this->channel_status[self::CHANNEL] === NET_SSH2_MSG_CHANNEL_CLOSE) {
                    $this->channel_close = \true;
                }
                $this->packet_type = \false;
                $this->packet_buffer = '';
                return \false;
            }
            $this->packet_buffer .= $temp;
            $tempLength -= strlen($temp);
        }
        $stop = microtime(\true);
        $this->packet_type = ord(Strings::shift($this->packet_buffer));
        if ($this->use_request_id) {
            extract(unpack('Npacket_id', Strings::shift($this->packet_buffer, 4)));
            $length -= 5;
        } else {
            $length -= 1;
        }
        $packet = Strings::shift($this->packet_buffer, $length);
        if (defined('Staatic\Vendor\NET_SFTP_LOGGING')) {
            $packet_type = '<- ' . self::$packet_types[$this->packet_type] . ' (' . round($stop - $start, 4) . 's)';
            $this->append_log($packet_type, $packet);
        }
        if (isset($request_id) && $this->use_request_id && $packet_id != $request_id) {
            $this->requestBuffer[$packet_id] = ['packet_type' => $this->packet_type, 'packet' => $packet];
            return $this->get_sftp_packet($request_id);
        }
        return $packet;
    }
    private function append_log($message_number, $message)
    {
        $this->append_log_helper(NET_SFTP_LOGGING, $message_number, $message, $this->packet_type_log, $this->packet_log, $this->log_size, $this->realtime_log_file, $this->realtime_log_wrap, $this->realtime_log_size);
    }
    public function getSFTPLog()
    {
        if (!defined('Staatic\Vendor\NET_SFTP_LOGGING')) {
            return \false;
        }
        switch (NET_SFTP_LOGGING) {
            case self::LOG_COMPLEX:
                return $this->format_log($this->packet_log, $this->packet_type_log);
                break;
            default:
                return $this->packet_type_log;
        }
    }
    public function getSFTPErrors()
    {
        return $this->sftp_errors;
    }
    public function getLastSFTPError()
    {
        return count($this->sftp_errors) ? $this->sftp_errors[count($this->sftp_errors) - 1] : '';
    }
    public function getSupportedVersions()
    {
        if (!($this->bitmap & SSH2::MASK_LOGIN)) {
            return \false;
        }
        if (!$this->partial_init) {
            $this->partial_init_sftp_connection();
        }
        $temp = ['version' => $this->defaultVersion];
        if (isset($this->extensions['versions'])) {
            $temp['extensions'] = $this->extensions['versions'];
        }
        return $temp;
    }
    public function getNegotiatedVersion()
    {
        if (!$this->precheck()) {
            return \false;
        }
        return $this->version;
    }
    public function setPreferredVersion($version)
    {
        $this->preferredVersion = $version;
    }
    protected function disconnect_helper($reason)
    {
        $this->pwd = \false;
        return parent::disconnect_helper($reason);
    }
    public function enableDatePreservation()
    {
        $this->preserveTime = \true;
    }
    public function disableDatePreservation()
    {
        $this->preserveTime = \false;
    }
}
