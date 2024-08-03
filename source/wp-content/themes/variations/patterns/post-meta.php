<?php
/**
 * Title: Post Meta
 * Slug: variations/post-meta
 * Description: Add a post meta section
 * Categories: query
 * Keywords: post meta
 * Block Types: core/template-part/post-meta
 */

?>
<!-- wp:spacer {"height":"0"} -->
<div style="height:0" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"8.01rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:8.01rem"><!-- wp:separator {"opacity":"css","align":"wide","className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-css-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"2.01rem"},"blockGap":"2.01rem"},"typography":{"fontSize":"1.01rem"}}} -->
<div class="wp-block-columns alignwide" style="margin-top:2.01rem;font-size:1.01rem"><!-- wp:column {"style":{"spacing":{"blockGap":"0px"}}} -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"0.5ch"}},"layout":{"type":"flex"}} -->
<div class="wp-block-group"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Posted', 'Verb to explain the publication status of a post', 'variations' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-date /-->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'in', 'Preposition to show the relationship between the post and its categories', 'variations' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"category"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.5ch"}},"layout":{"type":"flex"}} -->
<div class="wp-block-group"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'by', 'Preposition to show the relationship between the post and its author', 'variations' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-author {"showAvatar":false} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"0px"}}} -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"0.5ch"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Tags:', 'Label for a list of post tags', 'variations' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"post_tag"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->