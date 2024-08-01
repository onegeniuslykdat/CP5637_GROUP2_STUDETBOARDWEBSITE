<?php
/**
 * Title: Posts List
 * Slug: variations/posts-list
 * Description: Add posts list
 * Categories: posts
 * Keywords: blog, news, posts, list
 * Block Types: core/query
 */

?>
<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"dimensions":{"minHeight":""}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:group {"style":{"spacing":{"padding":{"top":"2.71rem","bottom":"2.71rem","left":"2.01rem","right":"2.01rem"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:2.71rem;padding-right:2.01rem;padding-bottom:2.71rem;padding-left:2.01rem"><!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"2.01rem","bottom":"2.01rem","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-style:none;border-width:0px;border-radius:0px;min-height:0px;margin-top:0;margin-bottom:0;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0"><!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"1.77rem"}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"0px","padding":{"right":"0","left":"0","top":"1.01rem","bottom":"1.01rem"},"margin":{"top":"0","bottom":"0"}},"typography":{"fontStyle":"normal","fontWeight":"500","fontSize":"1.13rem"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:1.01rem;padding-right:0;padding-bottom:1.01rem;padding-left:0;font-size:1.13rem;font-style:normal;font-weight:500"><!-- wp:post-author {"showBio":false,"byline":"by","style":{"layout":{"selfStretch":"fill","flexSize":null}}} /-->

<!-- wp:post-date {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"selfStretch":"fit","flexSize":null},"typography":{"fontSize":"1.01rem"}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-terms {"term":"category","prefix":"Categories: ","style":{"typography":{"fontStyle":"normal","fontWeight":"500","fontSize":"1.01rem"},"spacing":{"padding":{"top":"0","left":"0","right":"0","bottom":"1rem"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"isLink":true} /-->

<!-- wp:post-content /-->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"2.01rem","bottom":"2.01rem"}}},"className":"is-style-separator-dotted"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-separator-dotted" style="margin-top:2.01rem;margin-bottom:2.01rem"/>
<!-- /wp:separator --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:query-pagination {"paginationArrow":"arrow","style":{"typography":{"fontSize":"1.13rem"}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->