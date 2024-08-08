<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Setting\Build\AdditionalUrlsSetting $setting
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 * @var array $attributes
 */
?>

<fieldset>
    <?php 
if (isset($attributes['composed'])) {
    ?>
        <label for="<?php 
    echo \esc_attr($setting->name());
    ?>"><?php 
    echo \esc_html($setting->label());
    ?></label><br>
    <?php 
}
?>
    <div
        data-staatic-component="CrawlItems"
        data-name="<?php 
echo \esc_attr($setting->name());
?>"
        data-placeholder-text="<?php 
\esc_attr_e('URL', 'staatic');
?>"
        data-add-button-text="<?php 
\esc_attr_e('Add URL', 'staatic');
?>"
        data-has-advanced-options="<?php 
echo ($attributes['hasAdvancedOptions'] ?? \true) ? 'true' : 'false';
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
