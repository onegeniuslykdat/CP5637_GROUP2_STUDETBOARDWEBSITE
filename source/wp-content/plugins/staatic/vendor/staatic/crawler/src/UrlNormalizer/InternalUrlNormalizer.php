<?php

namespace Staatic\Crawler\UrlNormalizer;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class InternalUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @var mixed[]
     */
    private $options;
    /**
     * @var UrlNormalizerInterface
     */
    private $decoratedNormalizer;
    public function __construct(array $options = [])
    {
        $this->options = array_merge(['removeScheme' => \true, 'lowercase' => \false, 'removeQuery' => \true, 'removeFragment' => \true], $options);
        $this->decoratedNormalizer = new BasicUrlNormalizer($this->options);
    }
    /**
     * @param UriInterface $url
     */
    public function normalize($url): UriInterface
    {
        $url = $this->decoratedNormalizer->normalize($url);
        if ($this->options['removeScheme'] && $url->getScheme()) {
            $url = $url->withScheme('');
        }
        if ($url->getUserInfo()) {
            $url = $url->withUserInfo('');
        }
        if ($url->getHost()) {
            $url = $url->withHost('');
        }
        if ($url->getPort()) {
            $url = $url->withPort(null);
        }
        if ($this->options['lowercase']) {
            $url = new Uri(mb_strtolower((string) $url));
        }
        return $url;
    }
}
