/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.tickets=tribe.tickets||{},tribe.tickets.commerce=tribe.tickets.commerce||{},tribe.tickets.commerce.notice=tribe.tickets.commerce.notice||{},function($,obj){"use strict";obj.selectors={hiddenElement:".tribe-common-a11y-hidden",item:".tribe-tickets__commerce-checkout-notice",content:".tribe-tickets__commerce-checkout-notice-content",title:".tribe-tickets-notice__title",container:'[data-js="tec-tickets-commerce-notice"]'},obj.show=$item=>{if(!$item.length)return;const $container=$item.parents(obj.selectors.container).eq(0);$item.trigger("beforeShowNotice.tecTicketsCommerce",[$container]),$item.show(),$item.trigger("aftershowNotice.tecTicketsCommerce",[$container])},obj.hide=$item=>{if(!$item.length)return;const $container=$item.parents(obj.selectors.container).eq(0);$item.trigger("beforeHideNotice.tecTicketsCommerce",[$container]),$item.hide(),$item.trigger("afterHideNotice.tecTicketsCommerce",[$container])},obj.populate=($item,title,content)=>{const $content=$item.find(obj.selectors.content),$title=$item.find(obj.selectors.title);if(!$item.length||!$content.length||!$title.length)return;const $container=$item.parents(obj.selectors.container).eq(0);title=void 0!==title?title:$container.data("noticeDefaultTitle"),content=void 0!==content?content:$container.data("noticeDefaultContent"),$item.trigger("beforePopulateNotice.tecTicketsCommerce",[$container]),title instanceof jQuery?$title.empty().append(title):$title.text(title),content instanceof jQuery?$content.empty().append(content):$content.text(content),$item.trigger("afterPopulateNotice.tecTicketsCommerce",[$container])}}(jQuery,tribe.tickets.commerce.notice);