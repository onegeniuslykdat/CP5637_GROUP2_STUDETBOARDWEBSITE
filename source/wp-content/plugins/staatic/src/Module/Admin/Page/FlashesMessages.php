<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

trait FlashesMessages
{
    /**
     * @param string $title
     * @param string $message
     * @param string|null $redirectUrl
     * @param int $redirectTimeout
     */
    public function renderFlashMessage($title, $message, $redirectUrl = null, $redirectTimeout = 3000): void
    {
        $redirectUrl = $redirectUrl ?: admin_url('index.php');
        $this->renderer->render(
            'admin/flash-message.php',
            compact('title', 'message', 'redirectUrl', 'redirectTimeout')
        );
    }
}
