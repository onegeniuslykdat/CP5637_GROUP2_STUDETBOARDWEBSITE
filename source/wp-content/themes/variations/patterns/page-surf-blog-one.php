<?php
 /**
 * Title: Blog Surf 1
 * Slug: variations/page-surf-blog-one
 * Description: Add Blog Surf 1 Template
 * Categories: posts
 * Keywords: blog, news, posts, grid
 * Block Types: core/query
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blog/surf-banner.jpg","id":4206,"dimRatio":0,"customOverlayColor":"#e9ceb0","focalPoint":{"x":1,"y":0.48},"minHeight":300,"minHeightUnit":"px","isDark":false,"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"80px"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-cover alignfull is-light" style="margin-top:0;margin-bottom:0;padding-top:0px;padding-bottom:80px;min-height:300px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#e9ceb0"></span><img class="wp-block-cover__image-background wp-image-4206" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/blog/surf-banner.jpg" style="object-position:100% 48%" data-object-fit="cover" data-object-position="100% 48%"/><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":"150px"} -->
<div style="height:150px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"elements":{"link":{"color":{"text":"#ffffff"}}},"typography":{"fontSize":"72px","fontStyle":"normal","fontWeight":"500"},"color":{"text":"#ffffff"}},"fontFamily":"suranna","extendedSettings":{"prompt":"Please create the first headline for the blog page. This will be the first thing any new visitor will see. It should be no more than two words."}} -->
<h1 class="wp-block-heading has-text-align-center has-text-color has-link-color has-suranna-font-family" style="color:#ffffff;font-size:72px;font-style:normal;font-weight:500">Latest Blog</h1>
<!-- /wp:heading --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"full","layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group alignfull"><!-- wp:query {"queryId":1,"query":{"perPage":"9","pages":"0","offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false,"taxQuery":{"category":[]}},"align":"full"} -->
<div class="wp-block-query alignfull"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"3.51rem","bottom":"2.01rem","right":"1.5rem","left":"1.5rem"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"0px"}},"className":"variations-posts-grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide variations-posts-grid" style="border-style:none;border-width:0px;border-radius:0px;min-height:0px;margin-top:0;margin-bottom:0;padding-top:3.51rem;padding-right:1.5rem;padding-bottom:2.01rem;padding-left:1.5rem"><!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"2.01rem"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"0px"},"border":{"width":"0px","style":"none","radius":"1px"}},"backgroundColor":"tertiary","layout":{"type":"default"}} -->
<div class="wp-block-group has-tertiary-background-color has-background" style="border-style:none;border-width:0px;border-radius:1px;min-height:0px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-featured-image {"isLink":true,"width":"","height":"364px","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} /-->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"2.01rem","bottom":"2.01rem","left":"2.01rem","right":"2.01rem"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:2.01rem;padding-right:2.01rem;padding-bottom:2.01rem;padding-left:2.01rem"><!-- wp:group {"style":{"spacing":{"blockGap":"0.21rem"},"dimensions":{"minHeight":"0px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="min-height:0px"><!-- wp:post-terms {"term":"category","textAlign":"center","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"selfStretch":"fit","flexSize":null},"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"0.91rem","textTransform":"uppercase","textDecoration":"none"},"color":{"text":"#e27013"},"elements":{"link":{"color":{"text":"#e27013"},":hover":{"color":{"text":"#e27013"}}}}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"8px","padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center","verticalAlignment":"top"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-date {"textAlign":"center","style":{"spacing":{"margin":{"right":"0","left":"0","top":"0","bottom":"0"},"padding":{"right":"0","left":"0","top":"0","bottom":"0"}},"typography":{"fontSize":"0.71rem"}}} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"textAlign":"center","isLink":true,"style":{"typography":{"textDecoration":"none","fontStyle":"normal","fontWeight":"400","lineHeight":"1.5","fontSize":"1.31rem"},"spacing":{"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"padding":{"top":"1.05rem"}},"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"},":hover":{"color":{"text":"#000000"}}}}}} /-->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"1.01rem"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:1.01rem"><!-- wp:read-more {"style":{"typography":{"textDecoration":"underline","textTransform":"uppercase","fontStyle":"normal","fontWeight":"400","fontSize":"0.81rem"},"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"spacing":{"padding":{"top":"0.21rem","right":"0.21rem","bottom":"0.21rem","left":"0.21rem"}}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"2.01rem"},"padding":{"top":"2.01rem","bottom":"2.01rem","left":"0","right":"0"},"blockGap":"0"},"dimensions":{"minHeight":""}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:2.01rem;padding-top:2.01rem;padding-right:0;padding-bottom:2.01rem;padding-left:0"><!-- wp:query-pagination {"paginationArrow":"arrow","showLabel":false,"align":"wide","style":{"color":{"text":"#e27013"},"elements":{"link":{"color":{"text":"#e27013"},":hover":{"color":{"text":"#e27013"}}}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->