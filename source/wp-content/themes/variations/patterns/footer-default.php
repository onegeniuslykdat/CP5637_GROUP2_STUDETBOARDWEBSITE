<?php
/**
 * Title: Default Footer
 * Slug: variations/footer-default
 * Description: Add a Default Footer
 * Categories: footer
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2.71rem","bottom":"2.71rem"}}},"layout":{"type":"constrained","contentSize":""}} -->
<div class="wp-block-group" style="padding-top:2.71rem;padding-bottom:2.71rem"><!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"customTextColor":"#000001","overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"fontSize":"1.13rem"},"spacing":{"blockGap":"1rem"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"right":"0","left":"0"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0"><!-- wp:heading {"textAlign":"center","level":4,"style":{"typography":{"fontSize":"1.4rem","textDecoration":"none"}}} -->
<h4 class="wp-block-heading has-text-align-center" style="font-size:1.4rem;text-decoration:none"><a href="/">Variations</a></h4>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.01rem"}}} -->
<p class="has-text-align-center" style="font-size:1.01rem"><?php printf(
	esc_html__( '© Copyright 2024 | Variations by %s', 'variations' ),
	'<a href="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '" rel="nofollow" data-type="link" data-id="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '">Tyler Moore</a>'
); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->