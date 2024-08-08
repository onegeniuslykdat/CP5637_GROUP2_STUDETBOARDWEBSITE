<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting $setting
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 * @var array $attributes
 */
?>

<fieldset>
    <div
    data-staatic-component="RedirectItems"
        data-name="<?php 
echo \esc_attr($setting->name());
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
