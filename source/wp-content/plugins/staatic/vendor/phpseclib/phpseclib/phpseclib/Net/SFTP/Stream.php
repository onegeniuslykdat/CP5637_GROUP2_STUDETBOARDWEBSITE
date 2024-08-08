<?php

namespace Staatic\Vendor\phpseclib3\Net\SFTP;

use Staatic\Vendor\phpseclib3\Crypt\Common\PrivateKey;
use Staatic\Vendor\phpseclib3\Net\SFTP;
use Staatic\Vendor\phpseclib3\Net\SSH2;
class Stream
{
    public static $instances;
    private $sftp;
    private $path;
    private $mode;
    private $pos;
    private $size;
    private $entries;
    private $eof;
    public $context;
    private $notification;
    public static function register($protocol = 'sftp')
    {
        if (in_array($protocol, stream_get_wrappers(), \true)) {
            return \false;
        }
        return stream_wrapper_register($protocol, get_called_class());
    }
    public function __construct()
    {
        if (defined('Staatic\Vendor\NET_SFTP_STREAM_LOGGING')) {
            echo "__construct()\r\n";
        }
    }
    protected function parse_path($path)
    {
        $orig = $path;
        extract(parse_url($path) + ['port' => 22]);
        if (isset($query)) {
            $path .= '?' . $query;
        } elseif (preg_match('/(\?|\?#)$/', $orig)) {
            $path .= '?';
        }
        if (isset($fragment)) {
            $path .= '#' . $fragment;
        } elseif ($orig[strlen($orig) - 1] == '#') {
            $path .= '#';
        }
        if (!isset($host)) {
            return \false;
        }
        if (isset($this->context)) {
            $context = stream_context_get_params($this->context);
            if (isset($context['notification'])) {
                $this->notification = $context['notification'];
            }
        }
        if (preg_match('/^{[a-z0-9]+}$/i', $host)) {
            $host = SSH2::getConnectionByResourceId($host);
            if ($host === \false) {
                return \false;
            }
            $this->sftp = $host;
        } else {
            if (isset($this->context)) {
                $context = stream_context_get_options($this->context);
            }
            if (isset($context[$scheme]['session'])) {
                $sftp = $context[$scheme]['session'];
            }
            if (isset($context[$scheme]['sftp'])) {
                $sftp = $context[$scheme]['sftp'];
            }
            if (isset($sftp) && $sftp instanceof SFTP) {
                $this->sftp = $sftp;
                return $path;
            }
            if (isset($context[$scheme]['username'])) {
                $user = $context[$scheme]['username'];
            }
            if (isset($context[$scheme]['password'])) {
                $pass = $context[$scheme]['password'];
            }
            if (isset($context[$scheme]['privkey']) && $context[$scheme]['privkey'] instanceof PrivateKey) {
                $pass = $context[$scheme]['privkey'];
            }
            if (!isset($user) || !isset($pass)) {
                return \false;
            }
            if (isset(self::$instances[$host][$port][$user][(string) $pass])) {
                $this->sftp = self::$instances[$host][$port][$user][(string) $pass];
            } else {
                $this->sftp = new SFTP($host, $port);
                $this->sftp->disableStatCache();
                if (isset($this->notification) && is_callable($this->notification)) {
                    call_user_func($this->notification, \STREAM_NOTIFY_CONNECT, \STREAM_NOTIFY_SEVERITY_INFO, '', 0, 0, 0);
                    call_user_func($this->notification, \STREAM_NOTIFY_AUTH_REQUIRED, \STREAM_NOTIFY_SEVERITY_INFO, '', 0, 0, 0);
                    if (!$this->sftp->login($user, $pass)) {
                        call_user_func($this->notification, \STREAM_NOTIFY_AUTH_RESULT, \STREAM_NOTIFY_SEVERITY_ERR, 'Login Failure', NET_SSH2_MSG_USERAUTH_FAILURE, 0, 0);
                        return \false;
                    }
                    call_user_func($this->notification, \STREAM_NOTIFY_AUTH_RESULT, \STREAM_NOTIFY_SEVERITY_INFO, 'Login Success', NET_SSH2_MSG_USERAUTH_SUCCESS, 0, 0);
                } else if (!$this->sftp->login($user, $pass)) {
                    return \false;
                }
                self::$instances[$host][$port][$user][(string) $pass] = $this->sftp;
            }
        }
        return $path;
    }
    private function _stream_open($path, $mode, $options, &$opened_path)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        $this->path = $path;
        $this->size = $this->sftp->filesize($path);
        $this->mode = preg_replace('#[bt]$#', '', $mode);
        $this->eof = \false;
        if ($this->size === \false) {
            if ($this->mode[0] == 'r') {
                return \false;
            } else {
                $this->sftp->touch($path);
                $this->size = 0;
            }
        } else {
            switch ($this->mode[0]) {
                case 'x':
                    return \false;
                case 'w':
                    $this->sftp->truncate($path, 0);
                    $this->size = 0;
            }
        }
        $this->pos = ($this->mode[0] != 'a') ? 0 : $this->size;
        return \true;
    }
    private function _stream_read($count)
    {
        switch ($this->mode) {
            case 'w':
            case 'a':
            case 'x':
            case 'c':
                return \false;
        }
        $result = $this->sftp->get($this->path, \false, $this->pos, $count);
        if (isset($this->notification) && is_callable($this->notification)) {
            if ($result === \false) {
                call_user_func($this->notification, \STREAM_NOTIFY_FAILURE, \STREAM_NOTIFY_SEVERITY_ERR, $this->sftp->getLastSFTPError(), NET_SFTP_OPEN, 0, 0);
                return 0;
            }
            call_user_func($this->notification, \STREAM_NOTIFY_PROGRESS, \STREAM_NOTIFY_SEVERITY_INFO, '', 0, strlen($result), $this->size);
        }
        if (empty($result)) {
            $this->eof = \true;
            return \false;
        }
        $this->pos += strlen($result);
        return $result;
    }
    private function _stream_write($data)
    {
        switch ($this->mode) {
            case 'r':
                return \false;
        }
        $result = $this->sftp->put($this->path, $data, SFTP::SOURCE_STRING, $this->pos);
        if (isset($this->notification) && is_callable($this->notification)) {
            if (!$result) {
                call_user_func($this->notification, \STREAM_NOTIFY_FAILURE, \STREAM_NOTIFY_SEVERITY_ERR, $this->sftp->getLastSFTPError(), NET_SFTP_OPEN, 0, 0);
                return 0;
            }
            call_user_func($this->notification, \STREAM_NOTIFY_PROGRESS, \STREAM_NOTIFY_SEVERITY_INFO, '', 0, strlen($data), strlen($data));
        }
        if ($result === \false) {
            return \false;
        }
        $this->pos += strlen($data);
        if ($this->pos > $this->size) {
            $this->size = $this->pos;
        }
        $this->eof = \false;
        return strlen($data);
    }
    private function _stream_tell()
    {
        return $this->pos;
    }
    private function _stream_eof()
    {
        return $this->eof;
    }
    private function _stream_seek($offset, $whence)
    {
        switch ($whence) {
            case \SEEK_SET:
                if ($offset < 0) {
                    return \false;
                }
                break;
            case \SEEK_CUR:
                $offset += $this->pos;
                break;
            case \SEEK_END:
                $offset += $this->size;
        }
        $this->pos = $offset;
        $this->eof = \false;
        return \true;
    }
    private function _stream_metadata($path, $option, $var)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        switch ($option) {
            case 1:
                $time = isset($var[0]) ? $var[0] : null;
                $atime = isset($var[1]) ? $var[1] : null;
                return $this->sftp->touch($path, $time, $atime);
            case 2:
            case 3:
                return \false;
            case 4:
                return $this->sftp->chown($path, $var);
            case 5:
                return $this->sftp->chgrp($path, $var);
            case 6:
                return $this->sftp->chmod($path, $var) !== \false;
        }
    }
    private function _stream_cast($cast_as)
    {
        return $this->sftp->fsock;
    }
    private function _stream_lock($operation)
    {
        return \false;
    }
    private function _rename($path_from, $path_to)
    {
        $path1 = parse_url($path_from);
        $path2 = parse_url($path_to);
        unset($path1['path'], $path2['path']);
        if ($path1 != $path2) {
            return \false;
        }
        $path_from = $this->parse_path($path_from);
        $path_to = parse_url($path_to);
        if ($path_from === \false) {
            return \false;
        }
        $path_to = $path_to['path'];
        if (!$this->sftp->rename($path_from, $path_to)) {
            if ($this->sftp->stat($path_to)) {
                return $this->sftp->delete($path_to, \true) && $this->sftp->rename($path_from, $path_to);
            }
            return \false;
        }
        return \true;
    }
    private function _dir_opendir($path, $options)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        $this->pos = 0;
        $this->entries = $this->sftp->nlist($path);
        return $this->entries !== \false;
    }
    private function _dir_readdir()
    {
        if (isset($this->entries[$this->pos])) {
            return $this->entries[$this->pos++];
        }
        return \false;
    }
    private function _dir_rewinddir()
    {
        $this->pos = 0;
        return \true;
    }
    private function _dir_closedir()
    {
        return \true;
    }
    private function _mkdir($path, $mode, $options)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        return $this->sftp->mkdir($path, $mode, $options & \STREAM_MKDIR_RECURSIVE);
    }
    private function _rmdir($path, $options)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        return $this->sftp->rmdir($path);
    }
    private function _stream_flush()
    {
        return \true;
    }
    private function _stream_stat()
    {
        $results = $this->sftp->stat($this->path);
        if ($results === \false) {
            return \false;
        }
        return $results;
    }
    private function _unlink($path)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        return $this->sftp->delete($path, \false);
    }
    private function _url_stat($path, $flags)
    {
        $path = $this->parse_path($path);
        if ($path === \false) {
            return \false;
        }
        $results = ($flags & \STREAM_URL_STAT_LINK) ? $this->sftp->lstat($path) : $this->sftp->stat($path);
        if ($results === \false) {
            return \false;
        }
        return $results;
    }
    private function _stream_truncate($new_size)
    {
        if (!$this->sftp->truncate($this->path, $new_size)) {
            return \false;
        }
        $this->eof = \false;
        $this->size = $new_size;
        return \true;
    }
    private function _stream_set_option($option, $arg1, $arg2)
    {
        return \false;
    }
    private function _stream_close()
    {
    }
    public function __call($name, array $arguments)
    {
        if (defined('Staatic\Vendor\NET_SFTP_STREAM_LOGGING')) {
            echo $name . '(';
            $last = count($arguments) - 1;
            foreach ($arguments as $i => $argument) {
                var_export($argument);
                if ($i != $last) {
                    echo ',';
                }
            }
            echo ")\r\n";
        }
        $name = '_' . $name;
        if (!method_exists($this, $name)) {
            return \false;
        }
        return $this->{$name}(...$arguments);
    }
}
