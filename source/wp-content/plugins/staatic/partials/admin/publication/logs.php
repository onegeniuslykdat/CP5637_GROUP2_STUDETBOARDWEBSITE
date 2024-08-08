<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Formatter $_formatter
 * @var Staatic\WordPress\Publication\Publication $publication
 * @var Staatic\WordPress\ListTable\WpListTable $listTable
 */
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage;
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage;

?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php 
echo \esc_html(\sprintf(
    /* translators: %s: Publication creation date. */
    \__('Publication "%s"', 'staatic'),
    $_formatter->date($publication->dateCreated())
));
?>
        <?php 
if ($publication->isPreview()) {
    ?>
            <?php 
    \esc_html_e('(Preview)', 'staatic');
    ?>
        <?php 
}
?>
    </h1>

    <a href="<?php 
echo \admin_url(\sprintf('admin.php?page=%s&id=%s', PublicationLogsExportPage::PAGE_SLUG, $publication->id()));
?>" class="page-title-action"><?php 
\_e('Export', 'staatic');
?></a>

    <hr class="wp-header-end">

    <?php 
$currentTab = 'logs';
$this->render('admin/publication/_header.php', \compact('publication', 'currentTab'));
?>

    <br>

    <?php 
$listTable->views();
?>

    <form id="logs-filter" method="get">
        <input type="hidden" name="page" value="<?php 
echo \esc_attr(PublicationLogsPage::PAGE_SLUG);
?>">
        <input type="hidden" name="id" value="<?php 
echo \esc_attr($publication->id());
?>">
        <input type="hidden" name="curview" value="<?php 
echo \esc_html($listTable->get_view());
?>">

        <?php 
$listTable->search_box(\__('Search logs', 'staatic'), 'log-entry');
?>
        <?php 
$listTable->display();
?>
    </form>

    <div id="ajax-response"></div>

    <br class="clear">
</div>
<?php 
