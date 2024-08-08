<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use RuntimeException;
use Staatic\WordPress\Service\Encrypter\InvalidValueException;
use Staatic\WordPress\Service\Encrypter\PossiblyUnencryptedValueException;

final class Encrypter
{
    /**
     * @var string
     */
    private $encryptionKey;

    private const ENCRYPTION_CIPHER = 'aes-256-cbc';

    public function __construct(string $encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    public function decrypt(string $value)
    {
        $payload = json_decode(base64_decode($value), \true);
        if (!is_array($payload)) {
            throw new PossiblyUnencryptedValueException("Invalid value supplied.");
        }
        if (!isset($payload['iv'], $payload['value'])) {
            throw new InvalidValueException('Unable to decrypt value.');
        }
        $result = openssl_decrypt(
            $payload['value'],
            self::ENCRYPTION_CIPHER,
            $this->encryptionKey,
            0,
            base64_decode($payload['iv'])
        );
        if ($result === \false) {
            throw new InvalidValueException('Unable to decrypt value.');
        }

        return $result;
    }

    public function encrypt($value): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::ENCRYPTION_CIPHER));
        $value = openssl_encrypt($value, self::ENCRYPTION_CIPHER, $this->encryptionKey, 0, $iv);
        if ($value === \false) {
            throw new RuntimeException('Unable to encrypt value.');
        }
        $json = json_encode([
            'iv' => base64_encode($iv),
            'value' => $value
        ], \JSON_UNESCAPED_SLASHES);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new RuntimeException('Unable to encrypt value.');
        }

        return base64_encode($json);
    }
}
