<?php

namespace Staatic\Crawler\UrlEvaluator;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriNormalizer;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class ExcludeRulesUrlEvaluator implements UrlEvaluatorInterface
{
    private const WILDCARD_PLACEHOLDER = '___STAATIC_WILDCARD___';
    /**
     * @var mixed[]
     */
    private $simpleExcludeRules = [];
    /**
     * @var mixed[]
     */
    private $wildcardExcludeRules = [];
    /**
     * @var mixed[]
     */
    private $regexExcludeRules = [];
    public function __construct(array $excludeUrls = [])
    {
        $this->initializeExcludeRules($excludeUrls);
    }
    private function initializeExcludeRules(array $excludeUrls): void
    {
        foreach ($excludeUrls as $excludeUrl) {
            if ($this->isRegexRule($excludeUrl)) {
                $this->regexExcludeRules[] = $this->regexRule($excludeUrl);
            } elseif (strpos($excludeUrl, '*') !== false) {
                $this->wildcardExcludeRules[] = $this->wildcardRule($excludeUrl);
            } else {
                $this->simpleExcludeRules[] = $this->simpleRule($excludeUrl);
            }
        }
    }
    private function isRegexRule(string $possiblePattern): bool
    {
        if (!(strncmp($possiblePattern, '~', strlen('~')) === 0 && substr_compare($possiblePattern, '~', -strlen('~')) === 0)) {
            return \false;
        }
        if (strlen($possiblePattern) <= 2) {
            return \false;
        }
        return @preg_match($possiblePattern, '') !== \false;
    }
    private function wildcardRule(string $excludeUrl): string
    {
        $excludeUrl = str_replace('*', self::WILDCARD_PLACEHOLDER, $excludeUrl);
        $excludeUrl = (string) $this->normalizedPathRelativeReference(new Uri($excludeUrl));
        return sprintf('~^%s$~i', str_replace(self::WILDCARD_PLACEHOLDER, '.+?', preg_quote($excludeUrl, '~')));
    }
    private function simpleRule(string $excludeUrl): string
    {
        return (string) $this->normalizedPathRelativeReference(new Uri($excludeUrl));
    }
    private function regexRule(string $excludeUrl): string
    {
        return "{$excludeUrl}i";
    }
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool
    {
        $pathRelativeReference = (string) $this->normalizedPathRelativeReference($resolvedUrl);
        return !$this->matchesExcludeRule($pathRelativeReference);
    }
    private function normalizedPathRelativeReference(UriInterface $url): UriInterface
    {
        $normalizations = UriNormalizer::CAPITALIZE_PERCENT_ENCODING | UriNormalizer::DECODE_UNRESERVED_CHARACTERS | UriNormalizer::CONVERT_EMPTY_PATH | UriNormalizer::REMOVE_DOT_SEGMENTS | UriNormalizer::REMOVE_DUPLICATE_SLASHES | UriNormalizer::SORT_QUERY_PARAMETERS;
        $normalizedUrl = UriNormalizer::normalize($url, $normalizations);
        return (new Uri())->withPath($normalizedUrl->getPath())->withQuery($normalizedUrl->getQuery())->withFragment($normalizedUrl->getFragment());
    }
    private function matchesExcludeRule(string $pathRelativeReference): bool
    {
        foreach ($this->simpleExcludeRules as $rule) {
            if (strcasecmp($pathRelativeReference, $rule) === 0) {
                return \true;
            }
        }
        foreach ($this->wildcardExcludeRules as $rule) {
            if (preg_match($rule, $pathRelativeReference) === 1) {
                return \true;
            }
        }
        foreach ($this->regexExcludeRules as $rule) {
            if (preg_match($rule, $pathRelativeReference) === 1) {
                return \true;
            }
        }
        return \false;
    }
}
