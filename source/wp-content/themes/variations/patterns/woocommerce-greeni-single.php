<?php
/**
 * Title: Greeni Single Product Page
 * Slug: variations/woocommerce-greeni-single
 * Description: Greeni single product for woocommerce product.
 * Categories: woocommerce
 * Keywords: woocommerce
 */

?> 
<!-- wp:group {"style":{"spacing":{"margin":{"top":"4rem"}}},"layout":{"inherit":true,"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:4rem"><!-- wp:woocommerce/store-notices /-->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"5rem","left":"5rem"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"600px","style":{"typography":{"lineHeight":"1.5","textTransform":"uppercase","fontSize":"10px"},"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}}}} -->
<div class="wp-block-column has-text-color has-link-color" style="color:#ffffff;font-size:10px;line-height:1.5;text-transform:uppercase;flex-basis:600px"><!-- wp:woocommerce/product-image-gallery {"align":"center","className":"is-style-greeni-style"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"500px"} -->
<div class="wp-block-column" style="flex-basis:500px"><!-- wp:post-terms {"term":"product_cat","style":{"color":{"text":"#9a9a9a"},"elements":{"link":{"color":{"text":"#9a9a9a"}}},"typography":{"textTransform":"uppercase","fontSize":"1rem"},"spacing":{"margin":{"bottom":"10px"}}}} /-->

<!-- wp:post-title {"level":1,"style":{"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}},"spacing":{"margin":{"top":"0px"}},"typography":{"fontSize":"2.4rem"}},"fontFamily":"young-serif-regular","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:group {"style":{"typography":{"fontSize":"13px"},"color":{"text":"#282828"},"elements":{"link":{"color":{"text":"#282828"}}}},"className":"is-rating-greeni-style","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-rating-greeni-style has-text-color has-link-color" style="color:#282828;font-size:13px"><!-- wp:woocommerce/product-rating {"isDescendentOfSingleProductTemplate":true,"className":"is-style-default"} /--></div>
<!-- /wp:group -->

<!-- wp:post-excerpt {"style":{"typography":{"fontSize":"1rem"},"color":{"text":"#686868"},"elements":{"link":{"color":{"text":"#686868"}}}},"__woocommerceNamespace":"woocommerce/product-query/product-summary"} /-->

<!-- wp:group {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"1.1rem"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="font-size:1.1rem;font-style:normal;font-weight:700"><!-- wp:woocommerce/product-price {"isDescendentOfSingleProductTemplate":true,"fontFamily":"inter","style":{"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}},"typography":{"fontSize":"24px"}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"elements":{"button":{"color":{"text":"#ffffff","background":"#282828"}},"link":{"color":{"text":"#282828"}}},"color":{"text":"#282828"}},"layout":{"type":"constrained"},"fontSize":"small"} -->
<div class="wp-block-group has-text-color has-link-color has-small-font-size" style="color:#282828"><!-- wp:woocommerce/add-to-cart-form {"className":"is-style-greeni-style"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"60px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:60px"><!-- wp:woocommerce/product-meta {"align":"wide","className":"is-style-greeni-style"} -->
<div class="wp-block-woocommerce-product-meta alignwide is-style-greeni-style"><!-- wp:group {"style":{"typography":{"fontSize":"1rem"},"color":{"text":"#848485"},"elements":{"link":{"color":{"text":"#282828"}}},"spacing":{"padding":{"top":"15px","bottom":"15px"},"margin":{"top":"30px"},"blockGap":"10px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group has-text-color has-link-color" style="color:#848485;margin-top:30px;padding-top:15px;padding-bottom:15px;font-size:1rem"><!-- wp:woocommerce/product-sku {"isDescendentOfSingleProductTemplate":true,"fontFamily":"inter","style":{"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"typography":{"fontSize":"1rem"}}} /-->

<!-- wp:post-terms {"term":"product_cat","prefix":"Category: ","style":{"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"typography":{"fontSize":"1rem"}}} /-->

<!-- wp:post-terms {"term":"product_tag","prefix":"Tags: ","style":{"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"typography":{"fontSize":"1rem"}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:woocommerce/product-meta --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","style":{"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"},":hover":{"color":{"text":"#000000"}}},"h2":{"color":{"text":"#165a0b"}}},"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"400"},"spacing":{"margin":{"top":"8rem","bottom":"4rem"},"padding":{"top":"40px"}},"border":{"top":{"color":"#d5d5d5","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-text-color has-link-color" style="border-top-color:#d5d5d5;border-top-width:1px;color:#000000;margin-top:8rem;margin-bottom:4rem;padding-top:40px;font-size:1rem;font-style:normal;font-weight:400"><!-- wp:woocommerce/product-details {"align":"wide","className":"is-style-greeni-style"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"8rem","bottom":"6rem"},"padding":{"top":"40px"}},"border":{"top":{"color":"#d5d5d5","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="border-top-color:#d5d5d5;border-top-width:1px;margin-top:8rem;margin-bottom:6rem;padding-top:40px"><!-- wp:woocommerce/related-products {"align":"wide"} -->
<div class="wp-block-woocommerce-related-products alignwide"><!-- wp:query {"queryId":0,"query":{"perPage":"3","pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false},"namespace":"woocommerce/related-products","lock":{"remove":true,"move":true}} -->
<div class="wp-block-query"><!-- wp:heading {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"3rem"}},"typography":{"fontSize":"2rem"},"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}}},"className":"heading-no-line","fontFamily":"young-serif-regular"} -->
<h2 class="wp-block-heading heading-no-line has-text-color has-link-color has-young-serif-regular-font-family" style="color:#165a0b;margin-top:var(--wp--preset--spacing--30);margin-bottom:3rem;font-size:2rem">
							Related products			</h2>
<!-- /wp:heading -->

<!-- wp:post-template {"style":{"typography":{"fontSize":"1rem"},"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}},"spacing":{"blockGap":"24px"}},"className":"products-block-post-template is-style-greeni-style","layout":{"type":"grid","columnCount":3},"__woocommerceNamespace":"woocommerce/product-query/product-template"} -->
<!-- wp:group {"style":{"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}},"typography":{"fontSize":"10px","lineHeight":"1.5"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-text-color has-link-color" style="color:#ffffff;font-size:10px;line-height:1.5"><!-- wp:woocommerce/product-image {"saleBadgeAlign":"left","isDescendentOfQueryLoop":true,"height":"390px","style":{"border":{"radius":"24px"}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"textAlign":"center","level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"500","fontSize":"1.2rem"},"spacing":{"margin":{"top":"1rem","bottom":"0px"}},"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}}},"__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center","style":{"spacing":{"margin":{"bottom":"1rem"}},"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}},"typography":{"fontSize":"14px"}}} /-->

<!-- wp:woocommerce/product-button {"textAlign":"center","isDescendentOfQueryLoop":true,"className":"is-style-outline","fontSize":"small","style":{"spacing":{"margin":{"bottom":"2rem"},"padding":{"top":"12px","bottom":"12px","right":"40px","left":"40px"}},"color":{"text":"#165a0b"},"elements":{"link":{"color":{"text":"#165a0b"}}},"border":{"radius":"40px"}}} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:woocommerce/related-products --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->