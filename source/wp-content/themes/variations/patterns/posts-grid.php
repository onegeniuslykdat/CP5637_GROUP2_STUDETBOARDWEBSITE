<?php
/**
 * Title: Posts Grid
 * Slug: variations/posts-grid
 * Description: Add posts grid
 * Categories: posts
 * Keywords: blog, news, posts, grid
 * Block Types: core/query
 */

?>
<!-- wp:query {"queryId":1,"query":{"perPage":10,"pages":"0","offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":true},"align":"full"} -->
<div class="wp-block-query alignfull"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"2.01rem","bottom":"2.01rem","left":"2.01rem","right":"2.01rem"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"0px"}},"className":"variations-posts-grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide variations-posts-grid" style="border-style:none;border-width:0px;border-radius:0px;min-height:0px;margin-top:0;margin-bottom:0;padding-top:2.01rem;padding-right:2.01rem;padding-bottom:2.01rem;padding-left:2.01rem"><!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"2.01rem"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"1px"}},"backgroundColor":"tertiary","layout":{"type":"default"}} -->
<div class="wp-block-group has-tertiary-background-color has-background" style="border-style:none;border-width:0px;border-radius:1px;min-height:0px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-featured-image {"isLink":true,"height":"230px","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} /-->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"2.01rem","bottom":"2.01rem","left":"2.01rem","right":"2.01rem"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:2.01rem;padding-right:2.01rem;padding-bottom:2.01rem;padding-left:2.01rem"><!-- wp:group {"style":{"spacing":{"blockGap":"1.01rem"},"dimensions":{"minHeight":"0px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="min-height:0px"><!-- wp:post-title {"isLink":true,"style":{"typography":{"textDecoration":"none","fontStyle":"normal","fontWeight":"600","lineHeight":1.3,"fontSize":"2.51rem"},"spacing":{"padding":{"bottom":"2.01rem"},"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}}},"textColor":"main"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"8px","padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-terms {"term":"category","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"selfStretch":"fit","flexSize":null},"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

<!-- wp:post-date {"style":{"spacing":{"margin":{"right":"0","left":"0","top":"0","bottom":"0"},"padding":{"right":"0","left":"0","top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-excerpt {"moreText":"Read More","excerptLength":10,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"selfStretch":"fit","flexSize":null},"typography":{"fontSize":"1.13rem"}},"className":"is-style-excerpt-truncate-3"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"2.01rem"},"padding":{"top":"2.01rem","bottom":"2.01rem","left":"0","right":"0"},"blockGap":"0"},"dimensions":{"minHeight":""}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:2.01rem;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0"><!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","textColor":"#000001","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:query -->