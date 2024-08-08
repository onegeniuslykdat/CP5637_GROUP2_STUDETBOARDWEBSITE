<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Framework\Result;
final class NetlifyConfigGenerator extends AbstractConfigGenerator
{
    /**
     * @var string
     */
    private $extra = '';
    /**
     * @var mixed[]
     */
    private $headerRules = [];
    /**
     * @var mixed[]
     */
    private $redirectRules = [];
    public function __construct(?string $notFoundPath = null, string $extra = '')
    {
        $this->extra = $extra;
        if ($notFoundPath) {
            $this->headerRules[$notFoundPath] = $this->generateRedirectRule('/*', new Uri($notFoundPath), 404, \false);
        }
    }
    /**
     * @param Result $result
     */
    public function processResult($result): void
    {
        if ($result->redirectUrl()) {
            $this->redirectRules[$result->url()->getPath()] = $this->generateRedirectRule($result->url()->getPath(), $result->redirectUrl(), $result->statusCode());
        } elseif ($this->hasNonStandardMimeType($result) || $this->hasNonUtf8Charset($result)) {
            $this->headerRules[$result->url()->getPath()] = $this->generateHeaderRulesForResult($result);
        }
    }
    public function getFiles(): iterable
    {
        return ['/netlify.toml' => $this->generateConfigFile()];
    }
    private function generateRedirectRule(string $path, UriInterface $redirectUrl, int $statusCode, bool $force = \true): string
    {
        $redirectRules = [sprintf('from = "%s"', $path), sprintf('to = "%s"', $redirectUrl), sprintf('status = %d', $statusCode), sprintf('force = %s', $force ? 'true' : 'false')];
        return sprintf("[[redirects]]\n  %s\n", implode("\n  ", $redirectRules));
    }
    private function generateHeaderRulesForResult(Result $result): string
    {
        $headerValues = [sprintf('Content-Type = "%s"', $result->charset() ? sprintf('%s; charset=%s', $result->mimeType(), $result->charset()) : $result->mimeType())];
        $headerRules = [sprintf('for = "%s"', $result->url()->getPath()), '[headers.values]', '  ' . implode('  ', $headerValues)];
        return sprintf("[[headers]]\n  %s\n", implode("\n  ", $headerRules));
    }
    private function generateConfigFile(): StreamInterface
    {
        $stream = Utils::streamFor();
        if ($this->extra) {
            $stream->write($this->extra . "\n");
        }
        foreach ($this->redirectRules as $redirectRule) {
            $stream->write($redirectRule);
        }
        foreach ($this->headerRules as $headerRule) {
            $stream->write($headerRule);
        }
        $stream->rewind();
        return $stream;
    }
}
