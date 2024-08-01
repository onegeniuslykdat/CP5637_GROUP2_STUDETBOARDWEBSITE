<?php
/**
 * Title: Pebble Footer
 * Slug: variations/pebble-footer
 * Description: Add a Footer With Site Title, Menu and Copyright
 * Categories: footer, column
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"elements":{"link":{"color":{"text":"#ffffff"},":hover":{"color":{"text":"#ffffff"}}}},"color":{"background":"#422f01","text":"#ffffff"}},"layout":{"type":"constrained","contentSize":""}} -->
<div class="wp-block-group has-text-color has-background has-link-color" style="color:#ffffff;background-color:#422f01;padding-top:80px;padding-bottom:80px"><!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"small"} -->
<div class="wp-block-columns alignwide has-small-font-size" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center","width":"480px","textColor":"base"} -->
<div class="wp-block-column is-vertically-aligned-center has-base-color has-text-color" style="flex-basis:480px"><!-- wp:image {"id":334,"width":"80px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/headers-footers/sugar-cube-pebble.png" alt="" class="wp-image-334" style="width:80px"/></figure>
<!-- /wp:image -->

<!-- wp:site-title {"style":{"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}},"typography":{"fontSize":"37px","fontStyle":"normal","fontWeight":"400"}}} /-->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"},"color":{"text":"#ffffff"}}} -->
<p class="has-text-color" style="color:#ffffff;font-size:16px">A team comprised of board certified accountants, AcuVista Accounting helps you handle accounting problems in the most accessible and convenient way.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"textColor":"white","overlayMenu":"never","layout":{"type":"flex","justifyContent":"right","orientation":"vertical"},"style":{"spacing":{"blockGap":"25px"},"typography":{"fontSize":"16px"}}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px","textTransform":"capitalize"},"spacing":{"margin":{"top":"40px"}},"color":{"text":"#ffffff"}}} -->
<p class="has-text-align-center has-text-color" style="color:#ffffff;margin-top:40px;font-size:14px;text-transform:capitalize">© Copyright 2024 | Variations by <a rel="nofollow" href="https://tyler.com/" data-type="link" data-id="https://tyler.com/">Tyler Moore</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->