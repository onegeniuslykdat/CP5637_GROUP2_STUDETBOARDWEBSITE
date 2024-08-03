<?php
/**
 * Title: Island About Page Title
 * Slug: variations/island-about-page-title
 * Description: Add a About Page Title
 * Categories: featured
 * Keywords: title
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/island/island-banner-about.jpg","id":2333,"dimRatio":0,"focalPoint":{"x":0.5,"y":1},"minHeight":30,"minHeightUnit":"rem","contentPosition":"center center","isDark":false,"align":"full","style":{"spacing":{"padding":{"top":"2%"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-cover alignfull is-light" style="padding-top:2%;min-height:30rem"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-2333" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/island/island-banner-about.jpg" style="object-position:50% 100%" data-object-fit="cover" data-object-position="50% 100%"/><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"8rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"top"}} -->
<div class="wp-block-group alignwide" style="min-height:8rem"><!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1","fontSize":"3.71rem"},"spacing":{"margin":{"bottom":"1.01rem","top":"0rem"}},"color":{"text":"#07277c"}},"fontFamily":"prata-regular","extendedSettings":{"prompt":""}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color has-prata-regular-font-family" style="color:#07277c;margin-top:0rem;margin-bottom:1.01rem;font-size:3.71rem;font-style:normal;font-weight:400;line-height:1"><?php printf( esc_html__( '%s', 'variations' ), __( 'About The Island', 'variations' ) ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->