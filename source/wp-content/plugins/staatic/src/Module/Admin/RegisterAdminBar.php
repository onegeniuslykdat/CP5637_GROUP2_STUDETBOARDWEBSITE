<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Crawler\UrlTransformer\OfflineUrlTransformer;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage;
use Staatic\WordPress\Module\Admin\Page\PublishPage;
use Staatic\WordPress\Module\Admin\Page\PublishSubsetPage;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\Formatter;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use WP_Admin_Bar;

final class RegisterAdminBar implements ModuleInterface
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var UrlTransformerFactory
     */
    private $urlTransformerFactory;

    /**
     * @var DestinationUrlSetting
     */
    private $destinationUrl;

    public function __construct(Formatter $formatter, PublicationRepository $publicationRepository, UrlTransformerFactory $urlTransformerFactory, DestinationUrlSetting $destinationUrl)
    {
        $this->formatter = $formatter;
        $this->publicationRepository = $publicationRepository;
        $this->urlTransformerFactory = $urlTransformerFactory;
        $this->destinationUrl = $destinationUrl;
    }

    public function hooks(): void
    {
        if (is_network_admin() || !current_user_can('staatic_publish')) {
            return;
        }
        // Load admin bar once WordPress, plugins and themes are loaded
        add_action('wp_loaded', [$this, 'loadAdminBar']);
    }

    public function loadAdminBar(): void
    {
        add_action('admin_bar_menu', [$this, 'adminBarMenuSetup'], 90);
    }

    /**
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function adminBarMenuSetup($wp_admin_bar): void
    {
        $currentPublicationId = get_option('staatic_current_publication_id');
        $wp_admin_bar->add_node([
            'id' => 'staatic-toolbar',
            'title' => $this->getToolbarTitle($currentPublicationId)
        ]);
        if ($currentPublicationId) {
            $wp_admin_bar->add_node([
                'parent' => 'staatic-toolbar',
                'id' => 'staatic-toolbar-publish-status',
                'title' => __('Publication Status', 'staatic'),
                'href' => admin_url(
                    sprintf('admin.php?page=%s&id=%s', PublicationSummaryPage::PAGE_SLUG, $currentPublicationId)
                )
            ]);
            $wp_admin_bar->add_node([
                'parent' => 'staatic-toolbar',
                'id' => 'staatic-toolbar-publish-cancel',
                'title' => __('Cancel Publication', 'staatic'),
                'href' => wp_nonce_url(
                    admin_url(sprintf('admin.php?page=%s&cancel=%s', PublishPage::PAGE_SLUG, $currentPublicationId)),
                    'staatic-publish_cancel'
                )
            ]);
        } else {
            $wp_admin_bar->add_node([
                'parent' => 'staatic-toolbar',
                'id' => 'staatic-toolbar-publish',
                'title' => __('Publish', 'staatic'),
                'href' => wp_nonce_url(
                    admin_url(sprintf('admin.php?page=%s', PublishPage::PAGE_SLUG)),
                    'staatic-publish'
                )
            ]);
            if (current_user_can('staatic_publish_subset')) {
                $wp_admin_bar->add_node([
                    'parent' => 'staatic-toolbar',
                    'id' => 'staatic-toolbar-publish-subset',
                    'title' => __('Publish Selection', 'staatic'),
                    'href' => admin_url(sprintf('admin.php?page=%s', PublishSubsetPage::PAGE_SLUG))
                ]);
            }
            if ($this->supportsPreviewPublications()) {
                $wp_admin_bar->add_node([
                    'parent' => 'staatic-toolbar',
                    'id' => 'staatic-toolbar-publish-preview',
                    'title' => __('Publish to Preview Site', 'staatic'),
                    'href' => wp_nonce_url(
                        admin_url(sprintf('admin.php?page=%s&preview=1', PublishPage::PAGE_SLUG)),
                        'staatic-publish'
                    )
                ]);
            }
            if (!is_admin() && $currentPublishedUrl = $this->getCurrentPublishedUrl()) {
                $wp_admin_bar->add_node([
                    'parent' => 'staatic-toolbar',
                    'id' => 'staatic-toolbar-view-page',
                    'title' => __('View on Static Site', 'staatic'),
                    'href' => $currentPublishedUrl,
                    'meta' => [
                        'target' => '_blank'
                    ]
                ]);
            }
            $activePublicationId = get_option('staatic_active_publication_id');
            if ($activePublicationId) {
                $activePublication = $this->publicationRepository->find($activePublicationId);
                $wp_admin_bar->add_node([
                    'parent' => 'staatic-toolbar',
                    'id' => 'staatic-toolbar-latest-publication',
                    'title' => __('Live Publication Details', 'staatic'),
                    'href' => admin_url(
                        sprintf('admin.php?page=%s&id=%s', PublicationSummaryPage::PAGE_SLUG, $activePublication->id())
                    )
                ]);
                $wp_admin_bar->add_node([
                    'parent' => 'staatic-toolbar',
                    'id' => 'staatic-toolbar-latest-publication-status',
                    'title' => sprintf('<em style="font-style: italic;">%s</em>', sprintf(
                        /* translators: %s: Last successful publication date. */
                        __('Live Publication: %s', 'staatic'),
                        $this->formatter->shortDate($activePublication->dateCreated())
                    ))
                ]);
            }
        }
    }

    private function getToolbarTitle($currentPublicationId): string
    {
        if ($currentPublicationId) {
            return sprintf(
                '<span class="ab-icon staatic-loading staatic-spin"></span><span class="ab-label">%s</span>',
                __('Staatic', 'staatic')
            );
        }

        return sprintf('<span class="ab-label">%s</span>', __('Staatic', 'staatic'));
    }

    private function supportsPreviewPublications(): bool
    {
        return apply_filters('staatic_deployment_strategy_supports_preview', \false);
    }

    private function getCurrentPublishedUrl(): ?string
    {
        $transformer = ($this->urlTransformerFactory)(new Uri($this->destinationUrl->value()));
        if ($transformer instanceof OfflineUrlTransformer) {
            return null;
        }

        return (string) $transformer->transform(new Uri($this->getCurrentUrl()))->transformedUrl();
    }

    private function getCurrentUrl(): string
    {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) {
            $protocol = 'https://';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
