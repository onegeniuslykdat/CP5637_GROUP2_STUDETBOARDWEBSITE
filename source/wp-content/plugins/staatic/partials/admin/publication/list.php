<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\ListTable\WpListTable $listTable
 */
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Module\Admin\Page\PublishPage;

global $title;
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php 
echo \esc_html($title);
?></h1>
    <a href="<?php 
echo \wp_nonce_url(\admin_url(\sprintf('admin.php?page=%s', PublishPage::PAGE_SLUG)), 'staatic-publish');
?>" class="page-title-action"><?php 
\_e('Publish now', 'staatic');
?></a>

    <hr class="wp-header-end">

    <?php 
$listTable->views();
?>

    <form id="builds-filter" method="get">
        <input type="hidden" name="page" value="<?php 
echo \esc_attr(PublicationsPage::PAGE_SLUG);
?>">
        <input type="hidden" name="curview" value="<?php 
echo \esc_html($listTable->get_view());
?>">

        <?php 
$listTable->search_box(\__('Search publications', 'staatic'), 'publication');
?>
        <?php 
$listTable->display();
?>
    </form>

    <br class="clear">
</div>

<script>
    jQuery(function($) {
        $('a.submitredeploy').click(function (event) {
            if (!confirm('<?php 
echo \esc_js(
    \__('Are you sure you want to (re)deploy this publication using the currently configured deployment method?', 'staatic')
);
?>')) {
                event.preventDefault();
            }
        });

        $('a.submitdelete').click(function (event) {
            if (!confirm('<?php 
echo \esc_js(\__('Are you sure you want to delete this publication?', 'staatic'));
?>')) {
                event.preventDefault();
            }
        });
    });
</script>
<?php 
