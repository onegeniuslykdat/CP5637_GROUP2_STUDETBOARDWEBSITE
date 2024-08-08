<?php

namespace Staatic\Crawler\ResponseHandler;

use ArrayIterator;
use IteratorAggregate;
use Staatic\Crawler\CrawlerInterface;
use Traversable;
final class ResponseHandlerCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $handlers = [];
    public function __construct(array $handlers = [])
    {
        $this->addHandlers($handlers);
    }
    public static function createDefaultFulfilledCollection(): self
    {
        return new self([new XmlSitemapTaggerResponseHandler(), new RedirectResponseHandler(), new HtmlResponseHandler(), new CssResponseHandler(), new XmlSitemapResponseHandler(), new RssResponseHandler(), new XmlResponseHandler(), new RobotsTxtResponseHandler()]);
    }
    public static function createDefaultRejectedCollection(): self
    {
        return new self([new HtmlResponseHandler(), new CssResponseHandler(), new XmlResponseHandler()]);
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->handlers);
    }
    /**
     * @param iterable $handlers
     */
    public function addHandlers($handlers): void
    {
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
    }
    /**
     * @param ResponseHandlerInterface $handler
     */
    public function addHandler($handler): void
    {
        $this->handlers[get_class($handler)] = $handler;
    }
    /**
     * @param CrawlerInterface $crawler
     */
    public function toChain($crawler): ResponseHandlerInterface
    {
        $initialInstance = null;
        $previousInstance = null;
        foreach ($this->handlers as $instance) {
            $instance->setCrawler($crawler);
            if ($previousInstance) {
                $previousInstance->setNext($instance);
            } else {
                $initialInstance = $instance;
            }
            $previousInstance = $instance;
        }
        return $initialInstance;
    }
}
