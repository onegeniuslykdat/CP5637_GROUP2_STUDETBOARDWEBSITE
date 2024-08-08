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
    <?php 
if (isset($attributes['composed'])) {
    ?>
        <label for="<?php 
    echo \esc_attr($setting->name());
    ?>"><?php 
    echo \esc_html($setting->label());
    ?></label><br>
    <?php 
} else {
    ?>
        <label for="<?php 
    echo \esc_attr($setting->name());
    ?>">
    <?php 
}
?>
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
            <?php 
echo isset($attributes['options']) ? 'list="' . \esc_attr($setting->name()) . '_options' . '"' : '';
?>
        >
        <?php 
if (isset($attributes['options'])) {
    ?>
            <datalist id="<?php 
    echo \esc_attr($setting->name());
    ?>_options">
                <?php 
    foreach ($attributes['options'] as $option => $label) {
        ?>
                    <option
                        value="<?php 
        echo \esc_attr($option);
        ?>"
                        <?php 
        if ($label) {
            ?>label="<?php 
            echo \esc_attr($label);
            ?>" <?php 
        }
        ?>
                    >
                <?php 
    }
    ?>
            </datalist>
        <?php 
}
?>
    <?php 
if (!isset($attributes['composed'])) {
    ?>
        </label>
    <?php 
}
?>
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
