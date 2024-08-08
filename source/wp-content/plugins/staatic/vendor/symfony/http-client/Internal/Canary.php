<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

use Closure;
final class Canary
{
    /**
     * @var Closure
     */
    private $canceller;
    public function __construct(Closure $canceller)
    {
        $this->canceller = $canceller;
    }
    public function cancel()
    {
        if (isset($this->canceller)) {
            $canceller = $this->canceller;
            unset($this->canceller);
            $canceller();
        }
    }
    public function __destruct()
    {
        $this->cancel();
    }
}
