<?php
/**
 * Title: Nature Contact Section
 * Slug: variations/nature-contact-section
 * Description: Add a contact section
 * Categories: featured
 * Keywords: contact page, contact us, contact
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","wideSize":"900px","contentSize":"900px"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"7.01rem","bottom":"2.01rem"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:7.01rem;margin-bottom:2.01rem"><!-- wp:column {"width":"33.33%","style":{"spacing":{"padding":{"right":"0","left":"0","bottom":"4.51rem"}}}} -->
<div class="wp-block-column" style="padding-right:0;padding-bottom:4.51rem;padding-left:0;flex-basis:33.33%"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php printf( esc_html__( '%s', 'variations' ), __( 'Contact', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:media-text {"mediaId":333,"mediaType":"image","mediaWidth":15,"isStackedOnMobile":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"2.71rem"}},"typography":{"fontSize":"1.01rem"}}} -->
<div class="wp-block-media-text" style="margin-top:2.71rem;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-size:1.01rem;grid-template-columns:15% auto"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/contact/phone-solid.png" alt="" class="wp-image-333 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"Content…"} -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( '(310) 736-8445', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:media-text {"mediaId":334,"mediaType":"image","mediaWidth":15,"isStackedOnMobile":false,"style":{"typography":{"fontSize":"1.01rem"},"spacing":{"margin":{"bottom":"0","top":"2.01rem"}}}} -->
<div class="wp-block-media-text" style="margin-top:2.01rem;margin-bottom:0;font-size:1.01rem;grid-template-columns:15% auto"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/contact/envelope-solid.png" alt="" class="wp-image-334 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"Content…"} -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'example@gmail.com', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:media-text {"mediaId":335,"mediaType":"image","mediaWidth":15,"isStackedOnMobile":false,"style":{"typography":{"fontSize":"1.01rem"},"spacing":{"margin":{"bottom":"0","top":"2.01rem"}}}} -->
<div class="wp-block-media-text" style="margin-top:2.01rem;margin-bottom:0;font-size:1.01rem;grid-template-columns:15% auto"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/contact/location-pin-solid.png" alt="" class="wp-image-335 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"Content…"} -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( '2727 Beach Rd.<br>Malibu, CA 90264', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:spacer {"height":"45px","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div style="margin-top:0;margin-bottom:0;height:45px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"style":{"spacing":{"padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}}} -->
<p style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><strong><?php printf( esc_html__( '%s', 'variations' ), __( 'Follow Us:', 'variations' ) ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"base","iconColorValue":"#ffffff","customIconBackgroundColor":"#74a84a","iconBackgroundColorValue":"#74a84a","style":{"spacing":{"blockGap":{"left":"15px"},"margin":{"top":"1.01rem","bottom":"1.01rem"}}}} -->
<ul class="wp-block-social-links has-icon-color has-icon-background-color" style="margin-top:1.01rem;margin-bottom:1.01rem"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"twitter"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php printf( esc_html__( '%s', 'variations' ), __( 'Let\'s Get in Touch', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"15px"} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'This is where you put in your contact form. You can download a contact form plugin then insert the shortcode.', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->