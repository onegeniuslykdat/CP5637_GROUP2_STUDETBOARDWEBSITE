<?php
/**
 * Title: Search Page
 * Slug: variations/page-search-two
 * Inserter: no
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blog/archive-hero.jpg","id":20784,"dimRatio":0,"overlayColor":"contrast","minHeightUnit":"vh","contentPosition":"center center","isDark":false,"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull is-light" style="padding-top:0px;padding-bottom:0rem"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-20784" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blog/archive-hero.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"50vh"},"spacing":{"padding":{"top":"50px","right":"0px","bottom":"0px","left":"0px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide" style="min-height:50vh;padding-top:50px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:group {"style":{"spacing":{"margin":{"top":"4.51rem"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group" style="margin-top:4.51rem"><!-- wp:query-title {"type":"search","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"color":{"text":"#ffffff"},"elements":{"link":{"color":{"text":"#ffffff"}}},"typography":{"fontSize":"2.6rem"}},"fontFamily":"hedvig-letters-serif-regular"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"3.71rem","bottom":"3.71rem","right":"2.01rem","left":"2.01rem"},"blockGap":"0"},"dimensions":{"minHeight":""}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:3.71rem;padding-right:2.01rem;padding-bottom:3.71rem;padding-left:2.01rem"><!-- wp:search {"label":"","placeholder":"Search site...","width":100,"widthUnit":"%","buttonText":"Search","buttonPosition":"button-inside","style":{"border":{"width":"1px"},"color":{"background":"#060606"},"typography":{"fontSize":"1rem"}},"borderColor":"main-dark","textColor":"base","className":"is-style-default"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"60px","right":"0","left":"0"},"blockGap":"0"}},"fontSize":"medium"} -->
<div class="wp-block-group has-medium-font-size" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:60px;padding-left:0"><!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"constrained","contentSize":null}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"grid","columnCount":1}} -->
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group"><!-- wp:columns {"isStackedOnMobile":false,"align":"full"} -->
<div class="wp-block-columns alignfull is-not-stacked-on-mobile"><!-- wp:column {"width":"100%"} -->
<div class="wp-block-column" style="flex-basis:100%"><!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","textDecoration":"none","fontSize":"12px","fontStyle":"normal","fontWeight":"700","letterSpacing":"1px"},"color":{"text":"#959e95"},"elements":{"link":{"color":{"text":"#959e95"}}}}} /-->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"bottom":"15px","top":"10px"}},"color":{"text":"#242925"},"elements":{"link":{"color":{"text":"#242925"}}},"typography":{"fontSize":"1.38rem"}},"fontFamily":"prata-regular"} /-->

<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}},"color":{"text":"#242925"},"elements":{"link":{"color":{"text":"#242925"}}},"typography":{"fontSize":"14px"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50px"><!-- wp:group {"style":{"dimensions":{"minHeight":"42px"},"border":{"radius":"50%","color":"#959e95","width":"1px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group has-border-color" style="border-color:#959e95;border-width:1px;border-radius:50%;min-height:42px"><!-- wp:read-more {"content":"\u003cimg class=\u0022wp-image-18846\u0022 style=\u0022width: 16px;\u0022 src=\u0022<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/arrow.png\u0022 alt=\u0022\u0022\u003e","style":{"spacing":{"padding":{"top":"7px","right":"12px","bottom":"7px","left":"10px"}}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"color":{"background":"#ebe9e9"},"spacing":{"margin":{"top":"16px","bottom":"16px"}}},"className":"is-style-wide"} -->
<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:16px;margin-bottom:16px;background-color:#ebe9e9;color:#ebe9e9"/>
<!-- /wp:separator -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"align":"center","style":{"color":{"text":"#959e95"},"elements":{"link":{"color":{"text":"#959e95"}}},"typography":{"fontSize":"1rem"}}} -->
<!-- wp:query-pagination-previous {"label":"\u003c Prev"} /-->

<!-- wp:query-pagination-numbers {"style":{"typography":{"fontSize":"1rem"}},"className":"is-style-rounded-numbers"} /-->

<!-- wp:query-pagination-next {"label":"Next \u003e"} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results {"fontSize":"medium"} -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"},"padding":{"top":"2.01rem","right":"0","bottom":"2.01rem","left":"0"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0">Sorry, nothing was found for that search term.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->