<?php
/**
 * Title: Cube Footer
 * Slug: variations/cube-footer
 * Description: Add a footer with Copyright
 * Categories: footer, column
 * Keywords: footer
 * Block Types: core/template-part/footer
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"50px","bottom":"50px"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group" style="padding-top:50px;padding-bottom:50px"><!-- wp:columns {"verticalAlignment":"bottom","align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"40px","left":"40px"}}},"fontSize":"small"} -->
<div class="wp-block-columns alignfull are-vertically-aligned-bottom has-small-font-size" style="margin-top:0;margin-bottom:0;padding-right:40px;padding-left:40px"><!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:site-title {"style":{"color":{"text":"#000000"},"elements":{"link":{"color":{"text":"#000000"}}},"typography":{"fontSize":"38px","fontStyle":"normal","fontWeight":"500"}}} /-->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"400"},"color":{"text":"#000000"}}} -->
<p class="has-text-color" style="color:#000000;font-size:16px;font-style:normal;font-weight:400">123 Demo Street<br>Copenhagen, Denmark</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"padding":{"right":"0","left":"0"}}}} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="padding-right:0;padding-left:0"><!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"400"},"color":{"text":"#000000"}}} -->
<p class="has-text-color" style="color:#000000;font-size:16px;font-style:normal;font-weight:400">(555) 555-5555<br>email@example.com</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->