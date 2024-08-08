<?php

namespace Staatic\Vendor\Symfony\Component\Config;

use Staatic\Vendor\Symfony\Component\Config\Resource\SelfCheckingResourceChecker;
class ConfigCache extends ResourceCheckerConfigCache
{
    /**
     * @var bool
     */
    private $debug;
    public function __construct(string $file, bool $debug)
    {
        $this->debug = $debug;
        $checkers = [];
        if (\true === $this->debug) {
            $checkers = [new SelfCheckingResourceChecker()];
        }
        parent::__construct($file, $checkers);
    }
    public function isFresh(): bool
    {
        if (!$this->debug && is_file($this->getPath())) {
            return \true;
        }
        return parent::isFresh();
    }
}
