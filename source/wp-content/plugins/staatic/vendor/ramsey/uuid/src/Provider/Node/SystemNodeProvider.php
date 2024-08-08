<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Node;

use Staatic\Vendor\Ramsey\Uuid\Exception\NodeException;
use Staatic\Vendor\Ramsey\Uuid\Provider\NodeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use function array_filter;
use function array_map;
use function array_walk;
use function count;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function preg_match_all;
use function reset;
use function str_contains;
use function str_replace;
use function strtolower;
use function strtoupper;
use function substr;
use const GLOB_NOSORT;
use const PREG_PATTERN_ORDER;
class SystemNodeProvider implements NodeProviderInterface
{
    private const IFCONFIG_PATTERN = '/[^:]([0-9a-f]{2}([:-])[0-9a-f]{2}(\2[0-9a-f]{2}){4})[^:]/i';
    private const SYSFS_PATTERN = '/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i';
    public function getNode(): Hexadecimal
    {
        $node = $this->getNodeFromSystem();
        if ($node === '') {
            throw new NodeException('Unable to fetch a node for this system');
        }
        return new Hexadecimal($node);
    }
    protected function getNodeFromSystem(): string
    {
        static $node = null;
        if ($node !== null) {
            return (string) $node;
        }
        $node = $this->getSysfs();
        if ($node === '') {
            $node = $this->getIfconfig();
        }
        $node = str_replace([':', '-'], '', $node);
        return $node;
    }
    protected function getIfconfig(): string
    {
        $disabledFunctions = strtolower((string) ini_get('disable_functions'));
        if (strpos($disabledFunctions, 'passthru') !== false) {
            return '';
        }
        $phpOs = constant('PHP_OS');
        ob_start();
        switch (strtoupper(substr($phpOs, 0, 3))) {
            case 'WIN':
                passthru('ipconfig /all 2>&1');
                break;
            case 'DAR':
                passthru('ifconfig 2>&1');
                break;
            case 'FRE':
                passthru('netstat -i -f link 2>&1');
                break;
            case 'LIN':
            default:
                passthru('netstat -ie 2>&1');
                break;
        }
        $ifconfig = (string) ob_get_clean();
        if (preg_match_all(self::IFCONFIG_PATTERN, $ifconfig, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $iface) {
                if ($iface !== '00:00:00:00:00:00' && $iface !== '00-00-00-00-00-00') {
                    return $iface;
                }
            }
        }
        return '';
    }
    protected function getSysfs(): string
    {
        $mac = '';
        $phpOs = constant('PHP_OS');
        if (strtoupper($phpOs) === 'LINUX') {
            $addressPaths = glob('/sys/class/net/*/address', GLOB_NOSORT);
            if ($addressPaths === \false || count($addressPaths) === 0) {
                return '';
            }
            $macs = [];
            array_walk($addressPaths, function (string $addressPath) use (&$macs): void {
                if (is_readable($addressPath)) {
                    $macs[] = file_get_contents($addressPath);
                }
            });
            $trim = 'trim';
            $macs = array_map($trim, $macs);
            $macs = array_filter($macs, function (string $address) {
                return $address !== '00:00:00:00:00:00' && preg_match(self::SYSFS_PATTERN, $address);
            });
            $mac = reset($macs);
        }
        return (string) $mac;
    }
}
