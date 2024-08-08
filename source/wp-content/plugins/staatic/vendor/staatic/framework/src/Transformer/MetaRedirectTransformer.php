<?php

namespace Staatic\Framework\Transformer;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
final class MetaRedirectTransformer implements TransformerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var string
     */
    private $template;
    public function __construct(?string $template = null)
    {
        $this->logger = new NullLogger();
        $this->template = $template ?: $this->defaultTemplate();
    }
    /**
     * @param Result $result
     */
    public function supports($result): bool
    {
        return $result->redirectUrl() && $result->mimeType() === 'text/html';
    }
    /**
     * @param Result $result
     * @param Resource $resource
     */
    public function transform($result, $resource): void
    {
        $this->logger->info("Applying meta redirect transformation on '{$result->url()}'");
        if ($resource->size() > 0) {
            $this->logger->debug("Skipping '{$result->url()}' due to existing content");
            return;
        }
        $replacedContent = $this->replacedContent((string) $resource->content(), $result->redirectUrl());
        $resource->replace(Utils::streamFor($replacedContent));
        $result->syncResource($resource);
    }
    private function replacedContent(string $source, UriInterface $redirectUrl): string
    {
        return sprintf($this->template, $redirectUrl);
    }
    private function defaultTemplate(): string
    {
        return <<<EOT
<html>
    <head>
        <title>Redirecting</title>
        <meta http-equiv="refresh" content="0;url=%s" />
    </head>
    <body></body>
</html>
EOT;
    }
}
