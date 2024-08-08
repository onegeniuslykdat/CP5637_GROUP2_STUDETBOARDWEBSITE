<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\WordPress\Service\Encrypter;

final class EncrypterFactory
{
    public function __invoke(): Encrypter
    {
        return new Encrypter($this->getEncryptionKey());
    }

    private function getEncryptionKey(): string
    {
        if (defined('STAATIC_KEY')) {
            return constant('STAATIC_KEY');
        }

        return constant('AUTH_KEY');
    }
}
