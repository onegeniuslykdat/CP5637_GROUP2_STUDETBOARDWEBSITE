<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Dce;

use Staatic\Vendor\Ramsey\Uuid\Exception\DceSecurityException;
use Staatic\Vendor\Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use function escapeshellarg;
use function preg_split;
use function str_getcsv;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;
use const PREG_SPLIT_NO_EMPTY;
class SystemDceSecurityProvider implements DceSecurityProviderInterface
{
    public function getUid()
    {
        static $uid = null;
        if ($uid instanceof IntegerObject) {
            return $uid;
        }
        if ($uid === null) {
            $uid = $this->getSystemUid();
        }
        if ($uid === '') {
            throw new DceSecurityException('Unable to get a user identifier using the system DCE ' . 'Security provider; please provide a custom identifier or ' . 'use a different provider');
        }
        $uid = new IntegerObject($uid);
        return $uid;
    }
    public function getGid()
    {
        static $gid = null;
        if ($gid instanceof IntegerObject) {
            return $gid;
        }
        if ($gid === null) {
            $gid = $this->getSystemGid();
        }
        if ($gid === '') {
            throw new DceSecurityException('Unable to get a group identifier using the system DCE ' . 'Security provider; please provide a custom identifier or ' . 'use a different provider');
        }
        $gid = new IntegerObject($gid);
        return $gid;
    }
    private function getSystemUid(): string
    {
        if (!$this->hasShellExec()) {
            return '';
        }
        switch ($this->getOs()) {
            case 'WIN':
                return $this->getWindowsUid();
            default:
                return trim((string) shell_exec('id -u'));
        }
    }
    private function getSystemGid(): string
    {
        if (!$this->hasShellExec()) {
            return '';
        }
        switch ($this->getOs()) {
            case 'WIN':
                return $this->getWindowsGid();
            default:
                return trim((string) shell_exec('id -g'));
        }
    }
    private function hasShellExec(): bool
    {
        $disabledFunctions = strtolower((string) ini_get('disable_functions'));
        return strpos($disabledFunctions, 'shell_exec') === false;
    }
    private function getOs(): string
    {
        $phpOs = constant('PHP_OS');
        return strtoupper(substr($phpOs, 0, 3));
    }
    private function getWindowsUid(): string
    {
        $response = shell_exec('whoami /user /fo csv /nh');
        if ($response === null) {
            return '';
        }
        $sid = str_getcsv(trim((string) $response))[1] ?? '';
        if (($lastHyphen = strrpos($sid, '-')) === \false) {
            return '';
        }
        return trim(substr($sid, $lastHyphen + 1));
    }
    private function getWindowsGid(): string
    {
        $response = shell_exec('net user %username% | findstr /b /i "Local Group Memberships"');
        if ($response === null) {
            return '';
        }
        $userGroups = preg_split('/\s{2,}/', (string) $response, -1, PREG_SPLIT_NO_EMPTY);
        $firstGroup = trim($userGroups[1] ?? '', "* \t\n\r\x00\v");
        if ($firstGroup === '') {
            return '';
        }
        $response = shell_exec('wmic group get name,sid | findstr /b /i ' . escapeshellarg($firstGroup));
        if ($response === null) {
            return '';
        }
        $userGroup = preg_split('/\s{2,}/', (string) $response, -1, PREG_SPLIT_NO_EMPTY);
        $sid = $userGroup[1] ?? '';
        if (($lastHyphen = strrpos($sid, '-')) === \false) {
            return '';
        }
        return trim(substr($sid, $lastHyphen + 1));
    }
}
