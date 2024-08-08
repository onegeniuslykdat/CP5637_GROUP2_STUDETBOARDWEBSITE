<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Setting\SettingInterface $setting
 * @var array $attributes
 */
?>

<fieldset>
    <legend class="screen-reader-text"><span><?php 
echo \esc_html($setting->label());
?></span></legend>
    <label for="<?php 
echo \esc_attr($setting->name());
?>">
        <input
            type="checkbox"
            name="<?php 
echo \esc_attr($setting->name());
?>"
            id="<?php 
echo \esc_attr($setting->name());
?>"
            value="1"
            <?php 
echo $setting->value() ? ' checked="checked"' : '';
?>
        >
        <?php 
echo \esc_html($setting->extendedLabel());
?>
    </label>
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
