<?php

namespace Staatic\Vendor;

/**
 * @var mixed $setting
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
        data-staatic-component="RetainPaths"
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
