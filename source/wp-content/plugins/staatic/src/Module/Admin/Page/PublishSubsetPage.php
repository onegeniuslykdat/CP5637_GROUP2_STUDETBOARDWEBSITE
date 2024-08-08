<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationManager;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;
use Staatic\WordPress\Util\WordpressEnv;

final class PublishSubsetPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var PublicationManager
     */
    private $publicationManager;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publish-subset';

    use TriggersPublications;

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationManager $publicationManager)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->publicationManager = $publicationManager;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addPage']);
    }

    public function addPage(): void
    {
        $this->navigation->addPage(
            __('Publish Selection', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_publish_subset',
            PublicationsPage::PAGE_SLUG
        );
    }

    public function render(): void
    {
        $errors = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('staatic-publish-subset');
            $urls = (string) ($_POST['urls'] ?? '');
            $paths = (string) ($_POST['paths'] ?? '');
            $errors = $this->publicationManager->validateSubsetRequest($urls, $paths);
            if (!$errors->has_errors()) {
                $deploy = !empty($_POST['deploy']);
                $this->triggerPublication(__('Publish Selection', 'staatic'), function () use ($urls, $paths, $deploy) {
                    return $this->createPublication($urls, $paths, $deploy);
                });

                return;
            }
        }
        $urls = $_POST['urls'] ?? '';
        $paths = $_POST['paths'] ?? '';
        $deploy = isset($_POST['deploy']) ? (bool) $_POST['deploy'] : \true;
        $rootPath = WordpressEnv::getWordpressPath();
        $rootUrlPath = '/' . trim(WordpressEnv::getWordpressUrlPath(), '/');
        $this->renderer->render(
            'admin/publish-subset.php',
            compact('urls', 'paths', 'deploy', 'errors', 'rootPath', 'rootUrlPath')
        );
    }

    private function createPublication(string $urls, string $paths, bool $deploy): Publication
    {
        $metadata = [
            'subset' => [
                'urls' => $urls,
                'paths' => $paths
            ]
        ];
        if (!$deploy) {
            $metadata['skipDeploy'] = \true;
        }

        return $this->publicationManager->createPublication($metadata);
    }
}
