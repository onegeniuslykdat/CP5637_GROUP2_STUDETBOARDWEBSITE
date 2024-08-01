<?php
/**
 * Title: Call to action
 * Slug: variations/cta
 * Description: Add a Call to action section
 * Categories: featured
 * Keywords: Call to action
 * Block Types: core/buttons
 */

?>
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.2","fontSize":"2.26rem"}}} -->
<p style="font-size:2.26rem;line-height:1.2"><?php echo esc_html_x( 'Got any book recommendations?', 'sample content for call to action', 'variations' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"typography":{"fontSize":"1.01rem"}}} -->
<div class="wp-block-button has-custom-font-size" style="font-size:1.01rem"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'Get In Touch', 'sample content for call to action button', 'variations' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->