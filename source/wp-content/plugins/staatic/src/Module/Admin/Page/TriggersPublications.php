<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Closure;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage;

trait TriggersPublications
{
    use FlashesMessages;

    /**
     * @param string $title
     * @param Closure $createCallback
     */
    public function triggerPublication($title, $createCallback): void
    {
        $message = null;
        $redirectUrl = null;
        if ($this->publicationManager->isPublicationInProgress()) {
            $message = __('Publication could not be started because another publication is pending.', 'staatic');
        } else {
            $publication = $createCallback();
            if ($this->publicationManager->claimPublication($publication)) {
                $this->publicationManager->initiateBackgroundPublisher($publication);
                $message = __('A new publication will be started and deployed automatically.', 'staatic');
                $redirectUrl = admin_url(
                    sprintf('admin.php?page=%s&id=%s', PublicationSummaryPage::PAGE_SLUG, $publication->id())
                );
            } else {
                $this->publicationManager->cancelPublication($publication);
                $message = __('Publication could not be started because another publication is pending.', 'staatic');
            }
        }
        $this->renderFlashMessage($title, $message, $redirectUrl);
    }
}
