<?php
/**
 * Title: Search Page
 * Slug: variations/page-search
 * Inserter: no
 */

?>
<!-- wp:group {"style":{"spacing":{"margin":{"top":"4.51rem"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group" style="margin-top:4.51rem"><!-- wp:query-title {"type":"search","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"2.71rem","bottom":"2.71rem","right":"2.01rem","left":"2.01rem"},"blockGap":"0"},"dimensions":{"minHeight":""}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:2.71rem;padding-right:2.01rem;padding-bottom:2.71rem;padding-left:2.01rem"><!-- wp:search {"label":"","placeholder":"Search site...","width":100,"widthUnit":"%","buttonText":"Search","style":{"border":{"width":"1px"}},"borderColor":"main-dark","backgroundColor":"light","textColor":"base","fontSize":"small"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"0","right":"0","left":"0"},"blockGap":"0"}},"fontSize":"medium"} -->
<div class="wp-block-group has-medium-font-size" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"constrained","contentSize":null}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"},"fontSize":"small"} -->
<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0"}}},"fontSize":"large"} /-->

<!-- wp:post-excerpt {"moreText":"<?php printf( esc_html__( '%s', 'variations' ), __( 'Read more', 'variations' ) ); ?>"} /-->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"2.01rem","bottom":"2.01rem"}}},"className":"is-style-default"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:2.01rem;margin-bottom:2.01rem"/>
<!-- /wp:separator -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"},"fontSize":"medium"} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results {"fontSize":"medium"} -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"},"padding":{"top":"2.01rem","right":"0","bottom":"2.01rem","left":"0"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0"><?php printf( esc_html__( '%s', 'variations' ), __( 'Sorry, nothing was found for that search term.', 'variations' ) ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->