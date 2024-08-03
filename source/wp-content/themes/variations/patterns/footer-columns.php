<?php
/**
 * Title: Footer With Columns
 * Slug: variations/footer-columns
 * Description: Add a Footer With Columns
 * Categories: footer, columns
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4.51rem","bottom":"2.71rem","right":"2.01rem","left":"2.01rem"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"className":"dark-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull dark-footer" style="margin-top:0;margin-bottom:0;padding-top:4.51rem;padding-right:2.01rem;padding-bottom:2.71rem;padding-left:2.01rem"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"4.51rem"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:column {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"2.01rem"},"border":{"width":"0px","style":"none"}}} -->
<div class="wp-block-column" style="border-style:none;border-width:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:site-logo {"className":"is-style-default","style":{"color":{}}} /-->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.13rem"}}} -->
<p style="font-size:1.13rem"><?php printf( esc_html__( '%s', 'variations' ), __( 'Please connect our social media resources to stay in touch and receive all the recent info about us. Thank you for being part of our community!', 'variations' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"tertiary","iconColorValue":"#F6F6F6","iconBackgroundColor":"secondary","iconBackgroundColorValue":"#345C00","style":{"spacing":{"blockGap":{"top":"0","left":"2.01rem"},"padding":{"top":"2.01rem","right":"0","bottom":"2.01rem","left":"0"},"margin":{"top":"0","right":"0","bottom":"0","left":"0"}}},"className":"is-style-default","layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"}} -->
<ul class="wp-block-social-links has-icon-color has-icon-background-color is-style-default" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0"><!-- wp:social-link {"url":"#","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0"},"border":{"width":"0px","style":"none"}}} -->
<div class="wp-block-column" style="border-style:none;border-width:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"stretch","justifyContent":"right"}} -->
<div class="wp-block-group" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:navigation {"layout":{"type":"flex","justifyContent":"right","orientation":"vertical"},"style":{"spacing":{"blockGap":"0"},"layout":{"selfStretch":"fit","flexSize":null}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"light","className":"is-style-separator-dotted is-style-default"} -->
<hr class="wp-block-separator alignwide has-text-color has-light-color has-alpha-channel-opacity has-light-background-color has-background is-style-separator-dotted is-style-default" style="margin-top:0;margin-bottom:0"/>
<!-- /wp:separator -->

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"2.71rem","right":"0","bottom":"0","left":"0"},"blockGap":"0","margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:2.71rem;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"0px"},"elements":{"link":{"color":{"text":"#0693e4"}}}},"textColor":"main-accent","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group has-main-accent-color has-text-color has-link-color" style="border-style:none;border-width:0px;border-radius:0px;min-height:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.01rem"},"color":{"text":"#0693e4"}}} -->
<p class="has-text-color" style="color:#0693e4;font-size:1.01rem"><?php printf(
	esc_html__( '© Copyright 2024 | Variations by %s | All rights reserved | Powered by WordPress', 'variations' ),
	'<a href="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '" rel="nofollow" data-type="link" data-id="' . esc_url( __( 'https://tyler.com/', 'variations' ) ) . '">Tyler Moore</a>'
); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->