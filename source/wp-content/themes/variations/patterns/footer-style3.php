<?php
/**
 * Title: Footer Style 3
 * Slug: variations/footer-style3
 * Description: Add a Footer Style3
 * Categories: footer
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"7.01rem","bottom":"7.01rem","left":"2.01rem","right":"2.01rem"},"blockGap":"2.71rem","margin":{"top":"0","bottom":"0"}}},"backgroundColor":"tertiary","layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group alignfull has-tertiary-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:7.01rem;padding-right:2.01rem;padding-bottom:7.01rem;padding-left:2.01rem"><!-- wp:group {"style":{"spacing":{"blockGap":"2.01rem"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:site-title {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.77rem"}}} /-->

<!-- wp:group {"style":{"elements":{"link":{"color":{"text":"#345c01"}}},"typography":{"fontSize":"1.13rem"},"color":{"text":"#345c01"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group has-text-color has-link-color" style="color:#345c01;font-size:1.13rem"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}}} -->
<p style="font-style:normal;font-weight:500"><a href="#"><?php printf( esc_html__( '%s', 'variations' ), __( 'Download', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}}} -->
<p style="font-style:normal;font-weight:500"><a href="#"><?php printf( esc_html__( '%s', 'variations' ), __( 'Website', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}}} -->
<p style="font-style:normal;font-weight:500"><a href="#"><?php printf( esc_html__( '%s', 'variations' ), __( 'Facebook', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}}} -->
<p style="font-style:normal;font-weight:500"><a href="#"><?php printf( esc_html__( '%s', 'variations' ), __( 'YouTube', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"#345c01"}}},"typography":{"fontStyle":"normal","fontWeight":"500","fontSize":"1.01rem"},"color":{"text":"#345c01"}}} -->
<p class="has-text-align-center has-text-color has-link-color" style="color:#345c01;font-size:1.01rem;font-style:normal;font-weight:500"><?php printf(
	esc_html__( '© Copyright 2024 | Variations by %s', 'variations' ),
	'<a href="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '" rel="nofollow" data-type="link" data-id="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '">Tyler Moore</a>'
); ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"base","iconColorValue":"#ffffff","iconBackgroundColor":"contrast","iconBackgroundColorValue":"#000000","style":{"spacing":{"blockGap":{"top":"20px","left":"20px"}}},"className":"is-style-default","layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-icon-color has-icon-background-color is-style-default"><!-- wp:social-link {"url":"#","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->