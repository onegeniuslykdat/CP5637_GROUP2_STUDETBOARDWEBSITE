<?php
/**
 * Title: Default Archive Product Page
 * Slug: variations/island-archive-product-page 
 * Description: Main Shop Design
 * Categories: woocommerce
 * Keywords: woocommerce
 * Block Types: core/query
 */
?>
<!-- wp:group {"style":{"spacing":{"margin":{"top":"4rem"},"padding":{"bottom":"80px"}}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:4rem;padding-bottom:80px"><!-- wp:woocommerce/breadcrumbs {"style":{"color":{"text":"#686868"},"elements":{"link":{"color":{"text":"#282828"},":hover":{"color":{"text":"#686868"}}}}}} /-->

<!-- wp:term-description {"align":"wide"} /-->

<!-- wp:woocommerce/store-notices /-->

<!-- wp:group {"className":"alignwide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide"><!-- wp:woocommerce/catalog-sorting {"style":{"layout":{"selfStretch":"fixed","flexSize":"360px"}}} /--></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":true,"__woocommerceAttributes":[],"__woocommerceStockStatus":["instock","outofstock","onbackorder"]},"namespace":"woocommerce/product-query","align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"className":"products-block-post-template","layout":{"type":"grid","columnCount":3},"fontSize":"small","__woocommerceNamespace":"woocommerce/product-query/product-template"} -->
<!-- wp:group {"style":{"typography":{"lineHeight":"1.5","fontSize":"10px"},"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-text-color has-link-color" style="color:#ffffff;font-size:10px;line-height:1.5"><!-- wp:woocommerce/product-image {"saleBadgeAlign":"left","isDescendentOfQueryLoop":true,"height":"385px"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.5rem"},"dimensions":{"minHeight":"10rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"space-between"}} -->
<div class="wp-block-group" style="min-height:10rem"><!-- wp:post-title {"textAlign":"center","level":3,"isLink":true,"style":{"typography":{"textTransform":"uppercase","fontSize":"16px","fontStyle":"normal","fontWeight":"500"},"spacing":{"margin":{"top":"1rem"}}},"__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center"} /-->

<!-- wp:woocommerce/product-button {"textAlign":"center","isDescendentOfQueryLoop":true,"fontSize":"small","style":{"spacing":{"margin":{"bottom":"2rem"}},"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}}}} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers {"style":{"typography":{"letterSpacing":"3px"}}} /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>
	No products were found matching your selection.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->