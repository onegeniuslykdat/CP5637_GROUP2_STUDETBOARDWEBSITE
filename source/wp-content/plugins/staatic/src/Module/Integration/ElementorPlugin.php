<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider\AdditionalPath;
use Staatic\Crawler\UrlTransformer\OfflineUrlTransformer;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
use Staatic\Framework\Transformer\TransformerInterface;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use Staatic\WordPress\Util\WordpressEnv;

final class ElementorPlugin implements ModuleInterface
{
    /**
     * @var UrlTransformerFactory
     */
    private $urlTransformerFactory;

    /**
     * @var DestinationUrlSetting
     */
    private $destinationUrl;

    private const READ_MAXIMUM_BYTES = 1024 * 1024 * 16;

    // 16MB
    public function __construct(UrlTransformerFactory $urlTransformerFactory, DestinationUrlSetting $destinationUrl)
    {
        $this->urlTransformerFactory = $urlTransformerFactory;
        $this->destinationUrl = $destinationUrl;
    }

    public function hooks(): void
    {
        add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    public function setupIntegration(): void
    {
        if (!$this->isPluginActive()) {
            return;
        }
        add_filter('staatic_additional_paths', [$this, 'registerElementorAssetsPaths']);
        $transformer = ($this->urlTransformerFactory)(new Uri($this->destinationUrl->value()));
        if ($transformer instanceof OfflineUrlTransformer) {
            add_filter('staatic_transformers', [$this, 'registerElementorTransformer']);
        }
    }

    /**
     * @param mixed[] $additionalPaths
     */
    public function registerElementorAssetsPaths($additionalPaths): array
    {
        $elementorPaths = [];
        if (defined('ELEMENTOR_ASSETS_PATH') && is_dir(\ELEMENTOR_ASSETS_PATH)) {
            $elementorPaths[] = \ELEMENTOR_ASSETS_PATH;
        }
        if (defined('ELEMENTOR_PRO_ASSETS_PATH') && is_dir(\ELEMENTOR_PRO_ASSETS_PATH)) {
            $elementorPaths[] = \ELEMENTOR_PRO_ASSETS_PATH;
        }
        $extraAdditionalPaths = [];
        foreach ($elementorPaths as $path) {
            $extraAdditionalPaths[$path] = new AdditionalPath($path, AdditionalPath::resolveUriBasePath(
                $path,
                WordpressEnv::getWordpressPath(),
                WordpressEnv::getWordpressUrlPath()
            ));
        }

        return array_merge(array_values($extraAdditionalPaths), $additionalPaths);
    }

    /**
     * @param mixed[] $transformers
     */
    public function registerElementorTransformer($transformers): array
    {
        // Offline URL transformer causes the assets URL within Elementor[Pro]FrontendConfig
        // to be malformed. This transformer corrects this.
        $transformers[] = new class(self::READ_MAXIMUM_BYTES) implements TransformerInterface, LoggerAwareInterface {
            /**
             * @var int
             */
            private $readMaximumBytes;

            use LoggerAwareTrait;

            public function __construct(int $readMaximumBytes)
            {
                $this->readMaximumBytes = $readMaximumBytes;
                $this->logger = new NullLogger();
            }

            /**
             * @param Result $result
             */
            public function supports($result): bool
            {
                return $result->mimeType() === 'text/html' && $result->size() > 0;
            }

            /**
             * @param Result $result
             * @param Resource $resource
             */
            public function transform($result, $resource): void
            {
                $this->logger->info("Applying Elementor transformer on '{$result->url()}'");
                $content = $resource->content()->read($this->readMaximumBytes);
                $resource->content()->rewind();
                $content = preg_replace_callback(
                    '~"assets":"[^"]*?wp-content\\\\/plugins\\\\/(?:elementor(?:-pro)?|pro-elements)\\\\/assets\\\\/index\.html"~',
                    function ($match) {
                    return str_replace('assets\/index.html', 'assets\/', $match[0]);
                },
                    $content,
                    -1,
                    $count
                );
                if (!$count) {
                    $this->logger->debug("Elementor config not found on '{$result->url()}'");

                    return;
                }
                $this->logger->debug("Elementor config corrected on '{$result->url()}: {$count} replacements'");
                $resource->replace(Utils::streamFor($content));
                $result->syncResource($resource);
            }
        };

        return $transformers;
    }

    private function isPluginActive(): bool
    {
        return defined('ELEMENTOR_VERSION');
    }
}
