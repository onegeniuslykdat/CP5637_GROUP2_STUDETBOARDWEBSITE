<?php

namespace Staatic\Crawler\UrlEvaluator;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class ChainUrlEvaluator implements UrlEvaluatorInterface
{
    /**
     * @var mixed[]
     */
    private $evaluators;
    public function __construct(array $evaluators)
    {
        $this->evaluators = $evaluators;
    }
    /**
     * @param UrlEvaluatorInterface $evaluator
     */
    public function addEvaluator($evaluator): void
    {
        $this->evaluators[] = $evaluator;
    }
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool
    {
        foreach ($this->evaluators as $evaluator) {
            if (!$evaluator->shouldCrawl($resolvedUrl, $context)) {
                return \false;
            }
        }
        return \true;
    }
}
