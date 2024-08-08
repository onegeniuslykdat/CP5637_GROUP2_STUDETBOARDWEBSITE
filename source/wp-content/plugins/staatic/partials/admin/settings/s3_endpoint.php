<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting $setting
 * @var \Staatic\WordPress\Service\Formatter $_formatter
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
        <div
            data-staatic-component="S3Endpoint"
            data-name="<?php 
echo \esc_attr($setting->name());
?>"
        ></div>
        <input
            type="text"
            name="<?php 
echo \esc_attr($setting->name());
?>"
            id="<?php 
echo \esc_attr($setting->name());
?>"
            value="<?php 
echo \esc_attr($setting->value());
?>"
            />
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
