<?php
/**
 * Title: Greeni Header
 * Slug: variations/greeni-header
 * Description: Header with logo, navigation
 * Categories: header
 * Keywords: header, nav, site logo
 * Block Types: core/template-part/header
 */

?>
<!-- wp:group {"style":{"position":{"type":""},"spacing":{"padding":{"top":"30px","bottom":"30px","right":"1rem","left":"1rem"},"margin":{"top":"0","bottom":"0"}},"border":{"bottom":{"width":"0px","style":"none"},"top":[],"right":[],"left":[]},"color":{"background":"#ffffff"}},"layout":{"type":"constrained","contentSize":"1400px"}} -->
<div class="wp-block-group has-background" style="border-bottom-style:none;border-bottom-width:0px;background-color:#ffffff;margin-top:0;margin-bottom:0;padding-top:30px;padding-right:1rem;padding-bottom:30px;padding-left:1rem"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:image {"id":18262,"sizeSlug":"large","linkDestination":"custom"} -->
<figure class="wp-block-image size-large"><a href="/"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/greeni/greeni-logo.svg" alt="" class="wp-image-18262"/></a></figure>
<!-- /wp:image -->

<!-- wp:navigation {"customTextColor":"#165a0b","icon":"menu","customOverlayBackgroundColor":"#165a0b","overlayTextColor":"white","layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"fontSize":"16px","letterSpacing":"1px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"}},"fontFamily":"bayon"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"10px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:buttons {"style":{"spacing":{"blockGap":"14px"}}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"width":"0px","style":"none"},"spacing":{"padding":{"left":"0px","right":"0px","top":"0px","bottom":"0px"}},"typography":{"lineHeight":"0"}},"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline" style="line-height:0"><a class="wp-block-button__link wp-element-button" href="/my-account" style="border-style:none;border-width:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><img class="wp-image-20869" style="width: 24px;" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/greeni/user.svg" alt=""></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag","priceColor":{"color":"#ffffff"},"iconColor":{"color":"#165a0b"},"productCountColor":{"color":"#fb7044"},"style":{"typography":{"fontSize":"14px"}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->