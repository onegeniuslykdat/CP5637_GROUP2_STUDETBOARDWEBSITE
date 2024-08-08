<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Setting\Build\PreviewUrlSetting $setting
 * @var array $attributes
 */
?>

<fieldset>
    <legend class="screen-reader-text"><span><?php 
echo \esc_html($setting->label());
?></span></legend>
    <div
        data-staatic-component="DestinationUrl"
        data-name="<?php 
echo \esc_attr($setting->name());
?>"
        data-hide-offline-url="true"
    ></div>
    <input
        type="text"
        class="regular-text code"
        name="<?php 
echo \esc_attr($setting->name());
?>"
        id="<?php 
echo \esc_attr($setting->name());
?>"
        value="<?php 
echo \esc_attr($setting->value());
?>"
    >
    <?php 
if ($setting->description()) {
    ?>
        <p class="description">
            <?php 
    echo \wp_kses_post($setting->description());
    ?>
        </p>
    <?php 
}
?>
</fieldset>
<?php 
