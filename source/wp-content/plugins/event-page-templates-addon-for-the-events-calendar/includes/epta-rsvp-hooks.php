<?php
/**
 * Action/filter hooks used for functions/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * tecset_event_tickets_rsvp
 *
 */
require_once EPTA_PLUGIN_DIR . 'includes/epta-rsvp-functions.php';
add_action( 'tecset_event_tickets_rsvp', 'tecset_inject_link_template', 4 );
add_action( 'tecset_event_tickets_rsvp', 'tecset_front_end_tickets_form', 10 );
