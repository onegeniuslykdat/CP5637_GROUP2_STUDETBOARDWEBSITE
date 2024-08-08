<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 * @var string|null $publicationId
 */
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage;

?>

<div id="staatic-publication-logs-widget">
    <?php 
if ($publicationId) {
    ?>
        <div
            data-staatic-component="PublicationLogs"
            data-id="<?php 
    echo \esc_attr($publicationId);
    ?>"
        ></div>
        <a href="<?php 
    echo \admin_url(\sprintf('admin.php?page=%s&id=%s', PublicationSummaryPage::PAGE_SLUG, $publicationId));
    ?>">
            <?php 
    \esc_html_e('Publication Details', 'staatic');
    ?>
        </a>
    <?php 
} else {
    ?>
        <p><?php 
    \esc_html_e('Nothing has been published yet.', 'staatic');
    ?></p>
    <?php 
}
?>
</div>
<?php 
