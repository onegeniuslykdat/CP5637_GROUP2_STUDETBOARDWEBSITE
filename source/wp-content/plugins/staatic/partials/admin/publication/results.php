<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Formatter $_formatter
 * @var Staatic\WordPress\Publication\Publication $publication
 * @var Staatic\WordPress\ListTable\WpListTable $listTable
 */
use Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage;

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
echo \admin_url(\sprintf('admin.php?page=%s&id=%s', PublicationDownloadPage::PAGE_SLUG, $publication->id()));
?>" class="page-title-action"><?php 
\_e('Download', 'staatic');
?></a>

    <hr class="wp-header-end">

    <?php 
$currentTab = 'results';
$this->render('admin/publication/_header.php', \compact('publication', 'currentTab'));
?>

    <br>

    <?php 
$listTable->views();
?>

    <form id="results-filter" method="get">
        <input type="hidden" name="page" value="<?php 
echo \esc_attr(PublicationResultsPage::PAGE_SLUG);
?>">
        <input type="hidden" name="id" value="<?php 
echo \esc_attr($publication->id());
?>">
        <input type="hidden" name="curview" value="<?php 
echo \esc_html($listTable->get_view());
?>">

        <?php 
$listTable->search_box(\__('Search resources', 'staatic'), 'result');
?>
        <?php 
$listTable->display();
?>
    </form>

    <div id="ajax-response"></div>

    <br class="clear">
</div>
<?php 
