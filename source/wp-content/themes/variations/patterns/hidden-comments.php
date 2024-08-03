<?php
/**
 * Title: Hidden Comments
 * Slug: variations/hidden-comments
 * Inserter: no
 */

?>
<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"2.71rem","right":"2.71rem","bottom":"2.71rem","left":"2.71rem"}}}} -->
<div class="wp-block-group" style="padding-top:2.71rem;padding-right:2.71rem;padding-bottom:2.71rem;padding-left:2.71rem">
	<!-- wp:comments -->
	<div class="wp-block-comments">
		<!-- wp:heading {"level":2} -->
		<h2><?php echo esc_html_x( 'Comments', 'Title of comments section', 'variations' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:comments-title {"level":3} /-->

		<!-- wp:comment-template -->
			<!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"2.71rem"}}}} -->
			<div class="wp-block-columns" style="margin-bottom:2.71rem">
				<!-- wp:column {"width":"40px"} -->
				<div class="wp-block-column" style="flex-basis:40px">
					<!-- wp:avatar {"size":40,"style":{"border":{"radius":"20px"}}} /-->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:comment-author-name /-->

					<!-- wp:group {"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"flex"}} -->
					<div class="wp-block-group" style="margin-top:0px;margin-bottom:0px">
						<!-- wp:comment-date /-->
						<!-- wp:comment-edit-link /-->
					</div>
					<!-- /wp:group -->

					<!-- wp:comment-content /-->

					<!-- wp:comment-reply-link /-->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		<!-- /wp:comment-template -->

		<!-- wp:comments-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
			<!-- wp:comments-pagination-previous /-->
			<!-- wp:comments-pagination-numbers /-->
			<!-- wp:comments-pagination-next /-->
		<!-- /wp:comments-pagination -->

	<!-- wp:post-comments-form /-->
	</div>
	<!-- /wp:comments -->
</div>
<!-- /wp:group -->
