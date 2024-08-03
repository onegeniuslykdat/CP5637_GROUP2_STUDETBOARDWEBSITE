<?php
/**
 * Title: Clay Header
 * Slug: variations/clay-header
 * Description: Header with site title, navigation and button
 * Categories: header
 * Keywords: header, nav, site title
 * Block Types: core/template-part/header
 */

?>
<!-- wp:group {"style":{"position":{"type":"sticky","top":"0px"},"spacing":{"padding":{"left":"1rem","right":"1rem"},"margin":{"top":"0","bottom":"0"}},"color":{"text":"#1f1f1f"},"typography":{"fontSize":"2.6rem","fontStyle":"normal","fontWeight":"400","lineHeight":"1.5"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group has-text-color" style="color:#1f1f1f;margin-top:0;margin-bottom:0;padding-right:1rem;padding-left:1rem;font-size:2.6rem;font-style:normal;font-weight:400;line-height:1.5"><!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"0rem","right":"0rem"},"blockGap":{"top":"1rem","left":"0.5rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile" style="padding-top:2rem;padding-right:0rem;padding-bottom:2rem;padding-left:0rem"><!-- wp:column {"verticalAlignment":"center","width":"25%","style":{"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;flex-basis:25%"><!-- wp:site-title {"level":2,"style":{"typography":{"fontSize":"1.8rem"},"color":{"text":"#1f1f1f"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"85%","style":{"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"blockGap":""}},"layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
<div class="wp-block-column" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;flex-basis:85%"><!-- wp:group {"style":{"spacing":{"blockGap":"2.5rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:navigation {"customTextColor":"#1f1f1f","icon":"menu","layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"fontSize":"1.1rem","fontStyle":"normal","fontWeight":"400","lineHeight":"1.8"},"layout":{"selfStretch":"fit","flexSize":null},"spacing":{"blockGap":"2.01rem"}}} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap","orientation":"horizontal"},"style":{"layout":{"selfStretch":"fit","flexSize":null},"spacing":{"margin":{"top":"0.2rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:0.2rem"><!-- wp:button {"style":{"border":{"radius":"0px","color":"#cccccc","width":"1px"},"typography":{"textTransform":"none","fontSize":"1.1rem","lineHeight":"1.5","fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"left":"2.2rem","right":"2.2rem","top":"0.8rem","bottom":"0.8rem"}},"color":{"text":"#1f1f1f"}},"className":"is-style-outline"} -->
<div class="wp-block-button has-custom-font-size is-style-outline" style="font-size:1.1rem;font-style:normal;font-weight:600;line-height:1.5;text-transform:none"><a class="wp-block-button__link has-text-color has-border-color wp-element-button" style="border-color:#cccccc;border-width:1px;border-radius:0px;color:#1f1f1f;padding-top:0.8rem;padding-right:2.2rem;padding-bottom:0.8rem;padding-left:2.2rem">Contact Us</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->