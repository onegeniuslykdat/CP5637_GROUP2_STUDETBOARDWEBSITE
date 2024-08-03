<?php
/**
 * Title: Header Style 3
 * Slug: variations/header-style3
 * Description: Header with slogan, button, site title and navigation
 * Categories: header
 * Keywords: header, nav, site title
 * Block Types: core/template-part/header
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"2.01rem","left":"2.01rem","top":"1.01rem","bottom":"1.01rem"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"color":{"text":"#000001","background":"#f6f6f7"}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="color:#000001;background-color:#f6f6f7;margin-top:0;margin-bottom:0;padding-top:1.01rem;padding-right:2.01rem;padding-bottom:1.01rem;padding-left:2.01rem"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"right":"0","left":"0","top":"0","bottom":"0"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"layout":{"selfStretch":"fit","flexSize":null},"typography":{"fontStyle":"normal","fontWeight":"500","fontSize":"1.01rem"}}} -->
<p style="font-size:1.01rem;font-style:normal;font-weight:500"><?php printf( esc_html__( '%s', 'variations' ), __( 'Got any book recommendations?', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"typography":{"fontSize":"1.01rem","fontStyle":"normal","fontWeight":"500"},"color":{"text":"#000001","background":"#9dff21"}}} -->
<div class="wp-block-button has-custom-font-size" style="font-size:1.01rem;font-style:normal;font-weight:500"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="color:#000001;background-color:#9dff21"><?php printf( esc_html__( '%s', 'variations' ), __( 'Get In Touch', 'variations' ) ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"#fffffd"},":hover":{"color":{"text":"#fffffd"}}}},"spacing":{"padding":{"top":"2.71rem","bottom":"2.71rem","left":"2.01rem","right":"2.01rem"},"margin":{"top":"0"}},"color":{"background":"#345c01"}},"textColor":"base","layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-color has-text-color has-background has-link-color" style="background-color:#345c01;margin-top:0;padding-top:2.71rem;padding-right:2.01rem;padding-bottom:2.71rem;padding-left:2.01rem"><!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal"}} -->
<div class="wp-block-group alignwide"><!-- wp:site-title {"style":{"typography":{"fontSize":"1.77rem"}}} /-->

<!-- wp:navigation {"icon":"menu","customTextColor":"#fffffd","customOverlayBackgroundColor":"#345c01","customOverlayTextColor":"#fffffd","layout":{"type":"flex","orientation":"horizontal"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.13rem"},"spacing":{"blockGap":"20px"}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->