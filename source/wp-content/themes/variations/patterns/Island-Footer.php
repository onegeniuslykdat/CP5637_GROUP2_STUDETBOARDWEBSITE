<?php
/**
 * Title: Island Footer
 * Slug: variations/island-footer
 * Description: Add a Island Footer
 * Categories: footer
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"1rem","right":"1rem"}},"typography":{"fontStyle":"normal","fontWeight":"400"}},"textColor":"base","layout":{"type":"constrained","contentSize":"1140px"},"fontFamily":"inter"} -->
<div class="wp-block-group has-base-color has-text-color has-inter-font-family" style="padding-top:2rem;padding-right:1rem;padding-bottom:2rem;padding-left:1rem;font-style:normal;font-weight:400"><!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"blockGap":{"top":"0px","left":"0px"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center","width":"","style":{"spacing":{"padding":{"right":"0","left":"0","top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-top:0px;padding-right:0;padding-bottom:0px;padding-left:0"><!-- wp:site-title {"textAlign":"left","style":{"color":{"text":"#041d55"},"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"1.61rem"},"elements":{"link":{"color":{"text":"#041d55"}}}},"fontFamily":"prata-regular"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%","style":{"border":{"width":"0px","style":"none"},"spacing":{"blockGap":"0px","padding":{"top":"1rem","bottom":"1rem"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="border-style:none;border-width:0px;padding-top:1rem;padding-bottom:1rem;flex-basis:55%"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}},"fontFamily":"work-sans-regular"} -->
<p class="has-text-color has-link-color has-work-sans-regular-font-family" style="color:#03081e;font-size:1rem;text-decoration:none"><a href="mailto:#"><?php printf( esc_html__( '%s', 'variations' ), __( 'info@Island.com', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}}} -->
<p class="has-text-color has-link-color" style="color:#03081e;font-size:1rem;text-decoration:none"> | </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}},"fontFamily":"work-sans-regular"} -->
<p class="has-text-color has-link-color has-work-sans-regular-font-family" style="color:#03081e;font-size:1rem;text-decoration:none"> <a href="tel:#"><?php printf( esc_html__( '%s', 'variations' ), __( '+123456789', 'variations' ) ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}}} -->
<p class="has-text-color has-link-color" style="color:#03081e;font-size:1rem;text-decoration:none"> | </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}},"fontFamily":"work-sans-regular"} -->
<p class="has-text-color has-link-color has-work-sans-regular-font-family" style="color:#03081e;font-size:1rem;text-decoration:none"><?php printf( esc_html__( '%s', 'variations' ), __( 'Seesreasse 21, Zurich', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"25%","layout":{"type":"default"}} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:25%"><!-- wp:paragraph {"align":"left","style":{"typography":{"textDecoration":"none","fontSize":"1rem"},"color":{"text":"#03081e"},"elements":{"link":{"color":{"text":"#03081e"},":hover":{"color":{"text":"#03081e"}}}}},"fontFamily":"work-sans-regular"} -->
<p class="has-text-align-left has-text-color has-link-color has-work-sans-regular-font-family" style="color:#03081e;font-size:1rem;text-decoration:none"><?php printf( esc_html__( '%s', 'variations' ), __( '© 2024 Island. All rights reserved.', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->