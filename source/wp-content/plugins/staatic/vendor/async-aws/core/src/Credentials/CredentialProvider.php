<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use Staatic\Vendor\AsyncAws\Core\Configuration;
interface CredentialProvider
{
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials;
}
