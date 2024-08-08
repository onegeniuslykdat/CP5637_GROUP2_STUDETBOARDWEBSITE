<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Setting\Build\AdditionalPathsSetting $setting
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 * @var array $attributes
 */
?>

<fieldset>
    <div
        data-staatic-component="AdditionalPaths"
        data-name="<?php 
echo \esc_attr($setting->name());
?>"
        data-root-path="<?php 
echo \esc_attr($attributes['rootPath']);
?>"
        data-root-url-path="<?php 
echo \esc_attr($attributes['rootUrlPath']);
?>"
        data-placeholder-text="<?php 
\esc_attr_e('Path', 'staatic');
?>"
        data-add-button-text="<?php 
\esc_attr_e('Add Path', 'staatic');
?>"
    ></div>
    <textarea
        class="large-text code"
        name="<?php 
echo \esc_attr($setting->name());
?>"
        id="<?php 
echo \esc_attr($setting->name());
?>"
        rows="4"
    ><?php 
echo \esc_html($setting->value());
?></textarea>
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
