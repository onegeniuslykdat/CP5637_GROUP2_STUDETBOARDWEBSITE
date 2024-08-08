<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cache;

use InvalidArgumentException;
use Staatic\Vendor\Psr\SimpleCache\CacheInterface;
use wpdb;

final class TransientCache implements CacheInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var int|null
     */
    private $defaultTtl;

    /**
     * @var string
     */
    private $prefix = 'staatic_';

    public function __construct(wpdb $wpdb, ?int $defaultTtl = null, string $prefix = 'staatic_')
    {
        $this->wpdb = $wpdb;
        $this->defaultTtl = $defaultTtl;
        $this->prefix = $prefix;
    }

    private function transientKey(string $key): string
    {
        return $this->prefix . md5($key);
    }

    public function get($key, $default = null)
    {
        $this->validateKey($key);
        $transientKey = $this->transientKey($key);
        $value = get_transient($transientKey);
        if ($value === \false) {
            return $default;
        }

        return maybe_unserialize($value);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->validateKey($key);
        $transientKey = $this->transientKey($key);
        $ttl = ($ttl === null) ? $this->defaultTtl : $ttl;

        return set_transient($transientKey, serialize($value), $ttl);
    }

    public function delete($key)
    {
        $this->validateKey($key);
        $transientKey = $this->transientKey($key);

        return delete_transient($transientKey);
    }

    public function clear()
    {
        $statement = $this->wpdb->prepare(
            "\n            DELETE FROM {$this->wpdb->prefix}options\n            WHERE option_name LIKE %s\n                OR option_name LIKE %s",
            $this->wpdb->esc_like("_transient_{$this->prefix}"),
            $this->wpdb->esc_like("_transient_timeout_{$this->prefix}")
        );
        $result = $this->wpdb->query($statement);

        return $result !== \false;
    }

    public function getMultiple($keys, $default = null)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        $success = \true;
        foreach ($values as $key => $value) {
            $result = $this->set($key, $value, $ttl);
            if ($success && $result === \false) {
                $success = \false;
            }
        }

        return $success;
    }

    public function deleteMultiple($keys)
    {
        $success = \true;
        foreach ($keys as $key) {
            $result = $this->delete($key);
            if ($success && $result === \false) {
                $success = \false;
            }
        }

        return $success;
    }

    public function has($key)
    {
        $this->validateKey($key);

        return $this->get($key) !== null;
    }

    private function validateKey($key)
    {
        if (!$key || !is_string($key)) {
            throw new InvalidArgumentException(sprintf('Supplied key was empty or non-string: %s', gettype($key)));
        }
    }
}
