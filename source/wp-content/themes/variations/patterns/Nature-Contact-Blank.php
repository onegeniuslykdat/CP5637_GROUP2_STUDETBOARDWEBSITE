<?php
/**
 * Title: Contact Blank Page
 * Slug: variations/nature-contact-blank
 * Description: Add an Contact Blank Page
 * Categories: contactpage
 * Keywords: contact page, contact us, contact
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blank/bg.png","id":10,"dimRatio":0,"minHeight":450,"minHeightUnit":"px","isDark":false,"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"850px","wideSize":"900px"}} -->
<div class="wp-block-cover alignfull is-light" style="padding-top:0;padding-bottom:0;min-height:450px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-10" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blank/bg.png" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"style":{"color":{"text":"#000001"}}} -->
<h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#000001"><?php printf( esc_html__( '%s', 'variations' ), __( 'Contact', 'variations' ) ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#000001"}}} -->
<p class="has-text-align-center has-text-color" style="color:#000001"><?php printf( esc_html__( '%s', 'variations' ), __( 'Subheading: Craft a compelling subheading that sparks curiosity.', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"4.51rem"},"margin":{"top":"4.51rem","bottom":"4.51rem"},"padding":{"right":"2.01rem","left":"2.01rem"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:4.51rem;margin-bottom:4.51rem;padding-right:2.01rem;padding-left:2.01rem"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"style":{"typography":{"fontSize":"35px"},"spacing":{"padding":{"bottom":"12px"}}},"textColor":"custom-color-1"} -->
<h2 class="wp-block-heading has-custom-color-1-color has-text-color" style="padding-bottom:12px;font-size:35px"><?php printf( esc_html__( '%s', 'variations' ), __( 'You can find us at', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"2.01rem","bottom":"2.01rem"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group" style="margin-top:2.01rem;margin-bottom:2.01rem"><!-- wp:image {"id":287,"width":"48px","height":"undefinedpx","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fit","flexSize":null},"border":{"width":"3px","color":"#1e293b","radius":"100px"}},"className":"is-style-default"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blank/call.png" alt="" class="has-border-color wp-image-287" style="border-color:#1e293b;border-width:3px;border-radius:100px;aspect-ratio:1;object-fit:cover;width:48px;height:undefinedpx"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"23px","fontStyle":"normal","fontWeight":"500"}}} -->
<h5 class="wp-block-heading" style="font-size:23px;font-style:normal;font-weight:500"><?php printf( esc_html__( '%s', 'variations' ), __( 'PHONE', 'variations' ) ); ?></h5>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( '+123 456 7890', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"2.01rem"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group" style="margin-bottom:2.01rem"><!-- wp:image {"id":293,"width":"48px","height":"undefinedpx","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fit","flexSize":null},"border":{"width":"3px","color":"#1e293b","radius":"100px"}},"className":"is-style-default"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blank/email.png" alt="" class="has-border-color wp-image-293" style="border-color:#1e293b;border-width:3px;border-radius:100px;aspect-ratio:1;object-fit:cover;width:48px;height:undefinedpx"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"23px","fontStyle":"normal","fontWeight":"500"}}} -->
<h5 class="wp-block-heading" style="font-size:23px;font-style:normal;font-weight:500"><?php printf( esc_html__( '%s', 'variations' ), __( 'EMAIL', 'variations' ) ); ?></h5>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'email@website.com', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"2.01rem"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group" style="margin-bottom:2.01rem"><!-- wp:image {"id":294,"width":"48px","height":"undefinedpx","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fit","flexSize":null},"border":{"width":"3px","color":"#1e293b","radius":"100px"}},"className":"is-style-default"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blank/maps.png" alt="" class="has-border-color wp-image-294" style="border-color:#1e293b;border-width:3px;border-radius:100px;aspect-ratio:1;object-fit:cover;width:48px;height:undefinedpx"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"23px","fontStyle":"normal","fontWeight":"500"}}} -->
<h5 class="wp-block-heading" style="font-size:23px;font-style:normal;font-weight:500"><?php printf( esc_html__( '%s', 'variations' ), __( 'ADDRESS', 'variations' ) ); ?></h5>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( '2727 Ocean Road, Malibu, CA, 90264', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"40px","bottom":"40px"}},"color":{"background":"#c9c9c945"}},"className":"is-style-wide"} -->
<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:40px;margin-bottom:40px;background-color:#c9c9c945;color:#c9c9c945"/>
<!-- /wp:separator -->

<!-- wp:social-links {"iconBackgroundColor":"custom-color-1","iconBackgroundColorValue":"#1e293b","openInNewTab":true,"className":"is-style-default"} -->
<ul class="wp-block-social-links has-icon-background-color is-style-default"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"youtube"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php printf( esc_html__( '%s', 'variations' ), __( 'Let\'s Get in Touch', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"15px"} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'This is where you put in your contact form.  You can download a contact form plugin then insert the shortcode.', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->