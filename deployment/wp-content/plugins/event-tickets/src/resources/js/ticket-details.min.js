/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

var tribe_ticket_details=tribe_ticket_details||{};!function($,obj){"use strict";var $document=$(document);obj.init=function(detailsElems){obj.event_listeners()},obj.selectors=[".tribe-tickets__item__details__summary--more",".tribe-tickets__item__details__summary--less"],obj.event_listeners=function(){$document.on("keyup",obj.selectors,(function(event){13===event.keyCode&&obj.toggle_open(event.target)})),$document.on("click",obj.selectors,(function(event){obj.toggle_open(event.target)}))},obj.toggle_open=function(trigger){if(trigger){var $trigger=$(trigger);if($trigger.hasClass("tribe-tickets__item__details__summary--more")||$trigger.hasClass("tribe-tickets__item__details__summary--less")){var $parent=$trigger.closest(".tribe-tickets__item__details__summary"),$target=$("#"+$trigger.attr("aria-controls"));if($target&&$parent){event.preventDefault();var onOff=!$parent.hasClass("tribe__details--open");$parent.toggleClass("tribe__details--open",onOff),$target.toggleClass("tribe__details--open",onOff)}}}},$((function(){var detailsElems=document.querySelectorAll(".tribe-tickets__item__details__summary");detailsElems.length&&obj.init(detailsElems)}))}(jQuery,tribe_ticket_details);