<?php

namespace Staatic\Vendor\AsyncAws\Core\AwsError;

use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\UnexpectedValue;
use Staatic\Vendor\AsyncAws\Core\Exception\UnparsableResponse;
final class JsonRpcAwsErrorFactory implements AwsErrorFactoryInterface
{
    use AwsErrorFactoryFromResponseTrait;
    /**
     * @param string $content
     * @param mixed[] $headers
     */
    public function createFromContent($content, $headers): AwsError
    {
        try {
            $body = json_decode($content, \true);
            return self::parseJson($body, $headers);
        } catch (Throwable $e) {
            throw new UnparsableResponse('Failed to parse AWS error: ' . $content, 0, $e);
        }
    }
    private static function parseJson(array $body, array $headers): AwsError
    {
        $code = null;
        $message = $body['message'] ?? $body['Message'] ?? null;
        if (isset($body['__type'])) {
            $parts = explode('#', $body['__type'], 2);
            $code = $parts[1] ?? $parts[0];
        }
        if (null !== $code || null !== $message) {
            return new AwsError($code, $message, null, null);
        }
        throw new UnexpectedValue('JSON does not contains AWS Error');
    }
}
