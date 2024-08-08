<?php
/**
 * Template functions
 *
 * Functions for the templating system.
 */
// namespace eptarsvpfunctions;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'Tribe__Tickets__Main' ) ) {
	function tecset_inject_link_template() {
		Tribe__Tickets__Tickets_View::instance()->inject_link_template();
	}

	function tecset_front_end_tickets_form() {
		$content = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/rsvp.php' );
		Tribe__Tickets__RSVP::get_instance()->front_end_tickets_form( $content );
	}
}
