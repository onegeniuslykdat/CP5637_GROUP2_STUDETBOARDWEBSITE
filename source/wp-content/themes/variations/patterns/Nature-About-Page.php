<?php
/**
 * Title: Nature About Page
 * Slug: variations/nature-about
 * Description: Add a About Page template
 * Categories: aboutpage
 * Keywords: about
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/about/About-banner-1.jpg","id":270,"dimRatio":0,"focalPoint":{"x":0.5,"y":1},"minHeight":500,"minHeightUnit":"px","isDark":false,"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull is-light" style="min-height:500px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-270" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/about/About-banner-1.jpg" style="object-position:50% 100%" data-object-fit="cover" data-object-position="50% 100%"/><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"2.01rem","left":"2.01rem","top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="padding-top:0;padding-right:2.01rem;padding-bottom:0;padding-left:2.01rem"><!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"6rem"},"color":{"text":"#345c01"}}} -->
<h1 class="wp-block-heading has-text-color" style="color:#345c01;font-size:6rem"><?php printf( esc_html__( '%s', 'variations' ), __( 'About', 'variations' ) ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"4.51rem","bottom":"4.51rem","left":"0","right":"0"},"blockGap":"2.71rem"}},"layout":{"type":"constrained","wideSize":"1150px","contentSize":"1150px"}} -->
<div class="wp-block-group alignwide" style="padding-top:4.51rem;padding-right:0;padding-bottom:4.51rem;padding-left:0"><!-- wp:media-text {"align":"wide","mediaId":311,"mediaType":"image","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":{"top":"2.01rem","left":"2.71rem"},"margin":{"right":"0","left":"0"}}}} -->
<div class="wp-block-media-text alignwide is-stacked-on-mobile" style="margin-right:0;margin-left:0;padding-right:0;padding-left:0"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/about/Our-Mission-min-1.jpg" alt="" class="wp-image-311 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"2.71rem","top":"4.51rem","bottom":"4.51rem"},"blockGap":"0.6rem"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:4.51rem;padding-right:0;padding-bottom:4.51rem;padding-left:2.71rem"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontSize":"2.26rem"}}} -->
<h2 class="wp-block-heading has-text-align-left" style="font-size:2.26rem"><?php printf( esc_html__( '%s', 'variations' ), __( 'OUR MISSION', 'variations' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'Hello, my name is Tyler Moore and with the help of many people I made this template. I made it so it is super easy to update and so that it flows perfectly with my tutorials. Lots of love and hundreds of hours went into making it. I hope you love it as much as I do.', 'variations' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php printf( esc_html__( '%s', 'variations' ), __( 'I wish you the best of luck with your business, enjoy the adventure.', 'variations' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"variations/nature-call-to-action"} /-->