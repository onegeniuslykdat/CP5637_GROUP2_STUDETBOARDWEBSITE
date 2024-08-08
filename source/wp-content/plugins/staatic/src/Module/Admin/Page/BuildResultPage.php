<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Module\ModuleInterface;

final class BuildResultPage implements ModuleInterface
{
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('wp_loaded', [$this, 'handle']);
    }

    public function handle(): void
    {
        if (!isset($_REQUEST['staatic']) || $_REQUEST['staatic'] !== 'result-download') {
            return;
        }
        $resultId = isset($_REQUEST['resultId']) ? sanitize_key($_REQUEST['resultId']) : null;
        if (!$resultId) {
            wp_die(__('Missing result id.', 'staatic'));
        }
        if (!$result = $this->resultRepository->find($resultId)) {
            wp_die(__('Invalid result.', 'staatic'));
        }
        if (!$resource = $this->resourceRepository->find($result->sha1())) {
            wp_die(__('Invalid resource.', 'staatic'));
        }
        $filename = basename($result->url()->getPath());
        header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
        header(sprintf('Content-Type: %s', $result->mimeType()));
        header(sprintf('Content-Length: %d', $result->size()));
        while (!$resource->content()->eof()) {
            echo $resource->content()->read(4096);
        }
        die;
    }
}
