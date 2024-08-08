<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

use Closure;
use Staatic\Vendor\Symfony\Component\Config\Util\XmlUtils;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class IniFileLoader extends FileLoader
{
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $this->container->fileExists($path);
        $result = parse_ini_file($path, \true);
        if (\false === $result || [] === $result) {
            throw new InvalidArgumentException(sprintf('The "%s" file is not valid.', $resource));
        }
        $result = parse_ini_file($path, \true, \INI_SCANNER_RAW);
        if (isset($result['parameters']) && \is_array($result['parameters'])) {
            foreach ($result['parameters'] as $key => $value) {
                if (\is_array($value)) {
                    $this->container->setParameter($key, array_map(Closure::fromCallable([$this, 'phpize']), $value));
                } else {
                    $this->container->setParameter($key, $this->phpize($value));
                }
            }
        }
        if ($this->env && \is_array($result['parameters@' . $this->env] ?? null)) {
            foreach ($result['parameters@' . $this->env] as $key => $value) {
                $this->container->setParameter($key, $this->phpize($value));
            }
        }
        return null;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        if (!\is_string($resource)) {
            return \false;
        }
        if (null === $type && 'ini' === pathinfo($resource, \PATHINFO_EXTENSION)) {
            return \true;
        }
        return 'ini' === $type;
    }
    /**
     * @return mixed
     */
    private function phpize(string $value)
    {
        if ($value !== $v = rtrim($value)) {
            $value = ('""' === substr_replace($v, '', 1, -1)) ? substr($v, 1, -1) : $v;
        }
        $lowercaseValue = strtolower($value);
        switch (\true) {
            case \defined($value):
                return \constant($value);
            case 'yes' === $lowercaseValue:
            case 'on' === $lowercaseValue:
                return \true;
            case 'no' === $lowercaseValue:
            case 'off' === $lowercaseValue:
            case 'none' === $lowercaseValue:
                return \false;
            case isset($value[1]) && ("'" === $value[0] && "'" === $value[\strlen($value) - 1] || '"' === $value[0] && '"' === $value[\strlen($value) - 1]):
                return substr($value, 1, -1);
            default:
                return XmlUtils::phpize($value);
        }
    }
}
