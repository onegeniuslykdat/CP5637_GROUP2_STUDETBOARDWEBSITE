<?php
/**
 * Title: Nature Hero
 * Slug: variations/nature-hero
 * Description: Add a hero section
 * Categories: featured
 * Keywords: hero, hero section
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/banners/Banner.jpg","id":129,"dimRatio":0,"overlayColor":"contrast","focalPoint":{"x":0.5,"y":0},"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"align":"full","layout":{"type":"constrained","contentSize":"px"}} -->
<div class="wp-block-cover alignfull is-light"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-129" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/banners/Banner.jpg" style="object-position:50% 0%" data-object-fit="cover" data-object-position="50% 0%"/><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":"125px"} -->
<div style="height:125px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"6rem","fontStyle":"normal","fontWeight":"500"},"color":{"text":"#2c541d"}}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#2c541d;font-size:6rem;font-style:normal;font-weight:500"><?php printf( esc_html__( '%s', 'variations' ), __( 'EARTH', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}},"typography":{"fontStyle":"normal","fontWeight":"200","letterSpacing":"0.3rem","fontSize":"1.77rem"},"color":{"text":"#000001"}}} -->
<p class="has-text-align-center has-text-color" style="color:#000001;margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-size:1.77rem;font-style:normal;font-weight:200;letter-spacing:0.3rem"><?php printf( esc_html__( '%s', 'variations' ), __( 'ENDLESS POTENTIAL', 'variations' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2.01rem","bottom":"2.01rem"},"blockGap":"0"}}} -->
<div class="wp-block-buttons" style="margin-top:2.01rem;margin-bottom:2.01rem"><!-- wp:button {"textColor":"base","style":{"spacing":{"padding":{"left":"50px","right":"50px","top":"9px","bottom":"9px"}},"color":{"background":"#74a84a"},"typography":{"fontSize":"1.13rem"}},"className":"is-style-fill"} -->
<div class="wp-block-button has-custom-font-size is-style-fill" style="font-size:1.13rem"><a class="wp-block-button__link has-base-color has-text-color has-background wp-element-button" style="background-color:#74a84a;padding-top:9px;padding-right:50px;padding-bottom:9px;padding-left:50px"><?php printf( esc_html__( '%s', 'variations' ), __( 'EXPLORE', 'variations' ) ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:spacer {"height":"231px"} -->
<div style="height:231px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div></div>
<!-- /wp:cover -->