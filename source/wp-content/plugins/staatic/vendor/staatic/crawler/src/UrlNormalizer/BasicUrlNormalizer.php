<?php

namespace Staatic\Crawler\UrlNormalizer;

use Staatic\Vendor\GuzzleHttp\Psr7\UriNormalizer;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class BasicUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @var mixed[]
     */
    private $options;
    public function __construct(array $options = [])
    {
        $this->options = array_merge(['removeQuery' => \true, 'removeFragment' => \true], $options);
    }
    /**
     * @param UriInterface $url
     */
    public function normalize($url): UriInterface
    {
        if ($this->options['removeFragment'] && $url->getFragment()) {
            $url = $url->withFragment('');
        }
        if ($this->options['removeQuery'] && $url->getQuery()) {
            $url = $url->withQuery('');
        }
        if ($url->getPath() === '') {
            $url = $url->withPath('/');
        }
        $normalizations = UriNormalizer::PRESERVING_NORMALIZATIONS | UriNormalizer::REMOVE_DUPLICATE_SLASHES | UriNormalizer::SORT_QUERY_PARAMETERS;
        return UriNormalizer::normalize($url, $normalizations);
    }
}
