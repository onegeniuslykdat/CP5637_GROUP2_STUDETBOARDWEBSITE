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
        <?php 
if ($setting->extendedLabel()) {
    ?>
            <?php 
    echo \esc_html($setting->extendedLabel());
    ?>
        <?php 
}
?>
        <select
            name="<?php 
echo \esc_attr($setting->name());
?>[]"
            id="<?php 
echo \esc_attr($setting->name());
?>"
            multiple
        >
            <?php 
foreach ($attributes['selectOptions'] as $optionName => $optionLabel) {
    ?>
                <option
                    <?php 
    echo \in_array($optionName, $setting->value()) ? 'selected' : '';
    ?>
                    value="<?php 
    echo \esc_attr($optionName);
    ?>"
                >
                    <?php 
    echo \esc_html($optionLabel);
    ?>
                </option>
            <?php 
}
?>
        </select>
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
