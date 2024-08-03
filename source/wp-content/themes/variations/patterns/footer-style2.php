<?php
/**
 * Title: Footer Style 2
 * Slug: variations/footer-style2
 * Description: Add a Footer Style2
 * Categories: footer
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"2.01rem","bottom":"2.01rem"}},"color":{"background":"#171717"},"typography":{"fontStyle":"normal","fontWeight":"400"}},"layout":{"type":"constrained","contentSize":""}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#171717;padding-top:2.01rem;padding-bottom:2.01rem;font-style:normal;font-weight:400"><!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"#fffffd"},":hover":{"color":{"text":"#fffffd"}}}},"typography":{"fontSize":"1.01rem"},"color":{"text":"#fffffd"}}} -->
<p class="has-text-align-center has-text-color has-link-color" style="color:#fffffd;font-size:1.01rem"><?php printf(
	esc_html__( '© Copyright 2024 | Variations by %s', 'variations' ),
	'<a href="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '" rel="nofollow" data-type="link" data-id="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '">Tyler Moore</a>'
); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"right":"0","left":"0"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0"><!-- wp:image {"align":"center","id":71,"sizeSlug":"large","linkDestination":"custom"} -->
<figure class="wp-block-image aligncenter size-large"><a href="/"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logos/logo-white.svg" alt="" class="wp-image-71"/></a></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"customTextColor":"#fffffd","overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->