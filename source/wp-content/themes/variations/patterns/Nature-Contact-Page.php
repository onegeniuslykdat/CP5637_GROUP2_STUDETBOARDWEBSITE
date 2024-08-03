<?php
/**
 * Title: Nature Contact Page
 * Slug: variations/nature-contact
 * Description: Add a Contact Page template
 * Categories: contactpage
 * Keywords: contact page, contact us, contact
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/about/About-banner-1.jpg","id":270,"dimRatio":0,"focalPoint":{"x":0.5,"y":1},"minHeight":500,"minHeightUnit":"px","isDark":false,"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull is-light" style="min-height:500px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-270" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/about/About-banner-1.jpg" style="object-position:50% 100%" data-object-fit="cover" data-object-position="50% 100%"/><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"2.01rem","left":"2.01rem","top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="padding-top:0;padding-right:2.01rem;padding-bottom:0;padding-left:2.01rem"><!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"6rem"},"color":{"text":"#345c01"}}} -->
<h1 class="wp-block-heading has-text-color" style="color:#345c01;font-size:6rem"><?php printf( esc_html__( '%s', 'variations' ), __( 'Contact', 'variations' ) ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:pattern {"slug":"variations/nature-contact-section"} /-->