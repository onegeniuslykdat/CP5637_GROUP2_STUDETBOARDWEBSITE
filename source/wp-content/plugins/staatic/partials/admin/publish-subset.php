<?php

namespace Staatic\Vendor;

/**
 * @var \WP_Error|null $errors
 * @var string $paths
 * @var bool $deploy
 * @var string $rootPath
 * @var string $rootUrlPath
 */
use Staatic\WordPress\Module\Admin\Page\PublishSubsetPage;

?>

<?php 
if (\is_wp_error($errors) && $errors->has_errors()) {
    ?>
    <div class="error">
        <p><?php 
    \_e('Error: ', 'staatic');
    echo \implode("</p>\n<p>" . \__('Error: ', 'staatic'), $errors->get_error_messages());
    ?></p>
    </div>
<?php 
}
?>

<div id="staatic-settings" class="wrap">
    <h1 class="wp-heading-inline"><?php 
\_e('Publish Selection', 'staatic');
?></h1>
    <hr class="wp-header-end">

    <form
        id="staatic-publish-subset"
        action="<?php 
echo \esc_url(\self_admin_url(\sprintf('admin.php?page=%s', PublishSubsetPage::PAGE_SLUG)));
?>"
        method="post" novalidate="novalidate"
    >
        <?php 
\wp_nonce_field('staatic-publish-subset');
?>

        <h2><?php 
\_e('URLs', 'staatic');
?></h2>

        <fieldset>
            <div
                data-staatic-component="CrawlItems"
                data-name="urls"
                data-default-mode="text"
                data-placeholder-text="<?php 
\esc_attr_e('URL', 'staatic');
?>"
                data-add-button-text="<?php 
\esc_attr_e('Add URL', 'staatic');
?>"
                data-advanced-options="false"
            ></div>
            <textarea
                class="large-text code"
                name="urls"
                id="urls"
                rows="4"
            ><?php 
echo \esc_html($urls);
?></textarea>
            <p class="description"><?php 
echo \sprintf(
    /* translators: %s: Example URLs. */
    \__('Add the (absolute or relative) URLs to be included in this publication.<br>%s', 'staatic'),
    \sprintf('%s: <code>%s</code>.', \__('Examples', 'staatic'), \implode('</code>, <code>', ['/', '/specific-page/']))
);
?></p>
        </fieldset>

        <h2><?php 
\_e('Filesystem Paths', 'staatic');
?></h2>

        <fieldset>
            <div
                data-staatic-component="AdditionalPaths"
                data-name="paths"
                data-root-path="<?php 
echo \esc_attr($rootPath);
?>"
                data-root-url-path="<?php 
echo \esc_attr($rootUrlPath);
?>"
                data-default-mode="text"
                data-placeholder-text="<?php 
\esc_attr_e('Path', 'staatic');
?>"
                data-add-button-text="<?php 
\esc_attr_e('Add Path', 'staatic');
?>"
            ></div>
            <textarea
                class="large-text code"
                name="paths"
                id="paths"
                rows="4"
            ><?php 
echo \esc_html($paths);
?></textarea>
            <p class="description"><?php 
echo \sprintf(
    /* translators: %s: Root path. */
    \__('Add the (filesystem) paths to be included in this publication.<br>Base path: <code>%s</code>', 'staatic'),
    \esc_html($rootPath)
);
?></p>
        </fieldset>

        <h2><?php 
\_e('Deployment', 'staatic');
?></h2>

        <fieldset>
            <legend class="screen-reader-text"><span><?php 
\_e('Deploy Publication', 'staatic');
?></span></legend>
            <label for="deploy">
                <input type="hidden" name="deploy" value="0">
                <input
                    type="checkbox"
                    name="deploy"
                    id="deploy"
                    value="1"
                    <?php 
echo $deploy ? ' checked="checked"' : '';
?>
                >
                <?php 
\_e('Deploy publication using configured deployment method', 'staatic');
?>
            </label>
            <p class="description">
                <?php 
\_e('Choose to automatically deploy the generated static files to your pre-configured deployment method upon completion of the publication.<br>As an alternative, you have the option to download these files in a zipfile format once the process is finished.', 'staatic');
?>
            </p>
        </fieldset>

        <?php 
\submit_button(\__('Publish', 'staatic'));
?>

    </form>
</div>

<script>
    jQuery(function($) {
        $('#submit').click(function (event) {
            const deploy = $('input[name=deploy]:checked').val();

            if (deploy && !confirm('<?php 
echo \esc_js(
    \__('This may delete resources depending on the configured deployment method. Are you sure you want to continue?', 'staatic')
);
?>')) {
                event.preventDefault();
            }
        });
    });
</script>
<?php 
