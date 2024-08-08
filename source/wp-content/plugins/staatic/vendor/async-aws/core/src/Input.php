<?php

namespace Staatic\Vendor\AsyncAws\Core;

abstract class Input
{
    public $region;
    protected function __construct(array $input)
    {
        $this->region = $input['@region'] ?? null;
    }
    /**
     * @param string|null $region
     */
    public function setRegion($region): void
    {
        $this->region = $region;
    }
    public function getRegion(): ?string
    {
        return $this->region;
    }
    abstract public function request(): Request;
}
