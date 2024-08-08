<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\MessageInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
final class Message
{
    public static function toString(MessageInterface $message): string
    {
        if ($message instanceof RequestInterface) {
            $msg = trim($message->getMethod() . ' ' . $message->getRequestTarget()) . ' HTTP/' . $message->getProtocolVersion();
            if (!$message->hasHeader('host')) {
                $msg .= "\r\nHost: " . $message->getUri()->getHost();
            }
        } elseif ($message instanceof ResponseInterface) {
            $msg = 'HTTP/' . $message->getProtocolVersion() . ' ' . $message->getStatusCode() . ' ' . $message->getReasonPhrase();
        } else {
            throw new InvalidArgumentException('Unknown message type');
        }
        foreach ($message->getHeaders() as $name => $values) {
            if (is_string($name) && strtolower($name) === 'set-cookie') {
                foreach ($values as $value) {
                    $msg .= "\r\n{$name}: " . $value;
                }
            } else {
                $msg .= "\r\n{$name}: " . implode(', ', $values);
            }
        }
        return "{$msg}\r\n\r\n" . $message->getBody();
    }
    public static function bodySummary(MessageInterface $message, int $truncateAt = 120): ?string
    {
        $body = $message->getBody();
        if (!$body->isSeekable() || !$body->isReadable()) {
            return null;
        }
        $size = $body->getSize();
        if ($size === 0) {
            return null;
        }
        $body->rewind();
        $summary = $body->read($truncateAt);
        $body->rewind();
        if ($size > $truncateAt) {
            $summary .= ' (truncated...)';
        }
        if (preg_match('/[^\pL\pM\pN\pP\pS\pZ\n\r\t]/u', $summary) !== 0) {
            return null;
        }
        return $summary;
    }
    public static function rewindBody(MessageInterface $message): void
    {
        $body = $message->getBody();
        if ($body->tell()) {
            $body->rewind();
        }
    }
    public static function parseMessage(string $message): array
    {
        if (!$message) {
            throw new InvalidArgumentException('Invalid message');
        }
        $message = ltrim($message, "\r\n");
        $messageParts = preg_split("/\r?\n\r?\n/", $message, 2);
        if ($messageParts === \false || count($messageParts) !== 2) {
            throw new InvalidArgumentException('Invalid message: Missing header delimiter');
        }
        [$rawHeaders, $body] = $messageParts;
        $rawHeaders .= "\r\n";
        $headerParts = preg_split("/\r?\n/", $rawHeaders, 2);
        if ($headerParts === \false || count($headerParts) !== 2) {
            throw new InvalidArgumentException('Invalid message: Missing status line');
        }
        [$startLine, $rawHeaders] = $headerParts;
        if (preg_match("/(?:^HTTP\\/|^[A-Z]+ \\S+ HTTP\\/)(\\d+(?:\\.\\d+)?)/i", $startLine, $matches) && $matches[1] === '1.0') {
            $rawHeaders = preg_replace(Rfc7230::HEADER_FOLD_REGEX, ' ', $rawHeaders);
        }
        $count = preg_match_all(Rfc7230::HEADER_REGEX, $rawHeaders, $headerLines, \PREG_SET_ORDER);
        if ($count !== substr_count($rawHeaders, "\n")) {
            if (preg_match(Rfc7230::HEADER_FOLD_REGEX, $rawHeaders)) {
                throw new InvalidArgumentException('Invalid header syntax: Obsolete line folding');
            }
            throw new InvalidArgumentException('Invalid header syntax');
        }
        $headers = [];
        foreach ($headerLines as $headerLine) {
            $headers[$headerLine[1]][] = $headerLine[2];
        }
        return ['start-line' => $startLine, 'headers' => $headers, 'body' => $body];
    }
    public static function parseRequestUri(string $path, array $headers): string
    {
        $hostKey = array_filter(array_keys($headers), function ($k) {
            $k = (string) $k;
            return strtolower($k) === 'host';
        });
        if (!$hostKey) {
            return $path;
        }
        $host = $headers[reset($hostKey)][0];
        $scheme = (substr($host, -4) === ':443') ? 'https' : 'http';
        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
    public static function parseRequest(string $message): RequestInterface
    {
        $data = self::parseMessage($message);
        $matches = [];
        if (!preg_match('/^[\S]+\s+([a-zA-Z]+:\/\/|\/).*/', $data['start-line'], $matches)) {
            throw new InvalidArgumentException('Invalid request string');
        }
        $parts = explode(' ', $data['start-line'], 3);
        $version = isset($parts[2]) ? explode('/', $parts[2])[1] : '1.1';
        $request = new Request($parts[0], ($matches[1] === '/') ? self::parseRequestUri($parts[1], $data['headers']) : $parts[1], $data['headers'], $data['body'], $version);
        return ($matches[1] === '/') ? $request : $request->withRequestTarget($parts[1]);
    }
    public static function parseResponse(string $message): ResponseInterface
    {
        $data = self::parseMessage($message);
        if (!preg_match('/^HTTP\/.* [0-9]{3}( .*|$)/', $data['start-line'])) {
            throw new InvalidArgumentException('Invalid response string: ' . $data['start-line']);
        }
        $parts = explode(' ', $data['start-line'], 3);
        return new Response((int) $parts[1], $data['headers'], $data['body'], explode('/', $parts[0])[1], $parts[2] ?? null);
    }
}
