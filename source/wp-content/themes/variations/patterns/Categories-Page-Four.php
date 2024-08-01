<?php
/**
 * Title: Categories 4
 * Slug: variations/categories-page-four
 * Description: Add a categories template
 * Categories: blogcategory
 * Keywords: category, categories, blog
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"40px","right":"20px","left":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="padding-top:0px;padding-right:20px;padding-bottom:40px;padding-left:20px"><!-- wp:query-title {"type":"archive","showPrefix":false,"style":{"typography":{"fontSize":"2.5rem"},"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"spacing":{"margin":{"top":"40px"}}},"fontFamily":"hedvig-letters-serif-regular"} /-->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"left":"20px","right":"20px","top":"0px","bottom":"80px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="padding-top:0px;padding-right:20px;padding-bottom:80px;padding-left:20px"><!-- wp:query {"queryId":12,"query":{"perPage":9,"pages":0,"offset":"0","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"35px"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"10px"}}} /-->

<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"0px"}},"color":{"text":"#898989"},"elements":{"link":{"color":{"text":"#898989"}}},"typography":{"fontSize":"14px"}}} /-->

<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"22px"},"spacing":{"margin":{"bottom":"10px","top":"10px"}}}} /-->

<!-- wp:read-more {"content":"\u003cstrong\u003eREAD MORE\u003c/strong\u003e →","style":{"color":{"text":"#07277c"},"elements":{"link":{"color":{"text":"#07277c"}}},"typography":{"fontSize":"13px"}}} /-->
<!-- /wp:post-template -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query-pagination {"align":"center","style":{"color":{"text":"#959e95"},"elements":{"link":{"color":{"text":"#959e95"}}},"typography":{"fontSize":"1rem"}}} -->
<!-- wp:query-pagination-previous {"label":"\u003c Prev"} /-->

<!-- wp:query-pagination-numbers {"style":{"typography":{"fontSize":"1rem"}},"className":"is-style-rounded-numbers"} /-->

<!-- wp:query-pagination-next {"label":"Next \u003e"} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->