<?php
/**
 * Single Event Page Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-event-page-builder-templates/single-event.php
 *
 * @package TribeEventsCalendar
 * @version  4.3
 */

 namespace eptasingleeventpage;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
/**
 * Template tag to get related posts for the current post.
 *
 * @param int $count number of related posts to return.
 * @param int|obj $post the post to get related posts to, defaults to current global $post
 *
 * @return array the related posts.
 */
	require_once EPTA_PLUGIN_DIR . 'includes/epta-function.php';
	$select_temp = \eptafunctions\epta_dynamic_class();

function epta_tribe_event_page_get_related_posts( $count = 3, $post = false ) {
	if ( class_exists( 'Tribe__Events__Pro__Main' ) && tribe_get_option( 'hideRelatedEvents', false ) == true ) {
		return;
	}
	$post_id    = '';
	$tags       = '';
	$categories = '';
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$post_id    = \Tribe__Events__Main::postIdHelper( $post );
		$tags       = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
		$categories = wp_get_object_terms( $post_id, \Tribe__Events__Main::TAXONOMY, array( 'fields' => 'ids' ) );
		if ( ! $tags && ! $categories ) {
			return;
		}
	}
	$args = array(
		'posts_per_page' => $count,
		'post__not_in'   => array( $post_id ),
		'eventDisplay'   => 'list',
		'tax_query'      => array( 'relation' => 'OR' ),
		
	);
	if ( $tags ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'post_tag',
			'field'    => 'id',
			'terms'    => $tags,
		);
	}
	if ( $categories ) {
		$args['tax_query'][] = array(
			'taxonomy' => \Tribe__Events__Main::TAXONOMY,
			'field'    => 'id',
			'terms'    => $categories,
		);
	}

	$args  = apply_filters( 'tribe_related_posts_args', $args );
	$posts = '';
	if ( $args ) {
		if ( class_exists( 'Tribe__Events__Query' ) ) {
			$posts = \Tribe__Events__Query::getEvents( $args );
		}
	} else {
		$posts = array();
	}
	return apply_filters( 'tribe_get_related_posts', $posts );
}

	$events_label_singular = tribe_get_event_label_singular();

	$events_label_plural = tribe_get_event_label_plural();

	$event_id      = get_the_ID();
	$tecset_url    = '';
	$tecset_pageid = intval( get_option( 'tec_tribe_single_event_page' ) );
	$tecset_slug   = get_post_meta( $tecset_pageid, 'epta-url', true );
if ( isset( $tecset_slug ) && ! empty( $tecset_slug ) ) {
	$tecset_url = esc_url( \eptafunctions\ect_get_url_by_slug( $tecset_slug ) );
}
if ( $select_temp == 'template-2' ) {
		require_once EPTA_PLUGIN_DIR . 'includes/epta-template/epta-template-2.php';
} else {
	require_once EPTA_PLUGIN_DIR . 'includes/epta-template/epta-template-1.php';
}
// require_once(EPTA_PLUGIN_DIR .'includes/epta-template/epta-template-2.php');



