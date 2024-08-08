<?php
$timeline_style = $attribute['style'];
if ( $template == 'timeline' ) {
	$timeline_style = 'style-1';
} elseif ( $template == 'classic-timeline' ) {
	$timeline_style = 'style-2';
}

/**Geting The Event Image Url*/

$ev_post_img = ect_get_event_image( $event_id, $size = 'large' );
if ( $i % 2 == 0 ) {
	$even_odd = 'even';
} else {
	$even_odd = 'odd';
}

if ( $timeline_style == 'style-1' ) {
	if ( $events_date_header !== '' ) {
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . wp_kses_post( $events_date_header ) . '</div>
						</div>';
	}

	$events_html .= '<div id="post-' . esc_attr( $event_id ) . '" class="ect-timeline-post ' . esc_attr( $even_odd ) . ' ' . esc_attr( $event_type ) . ' ' . esc_attr( $timeline_style ) . ' ' . $time . '">';
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content ' . esc_attr( $even_odd ) . '">';
	$events_html .= '<div class="ect-timeline-header">';
	if ( $ev_post_img ) {
		$events_html .= '<a class="timeline-ev-img" href="' . esc_url( tribe_get_event_link( $event_id ) ) . '"><img src= "' . $ev_post_img . '"/></a>';
	}
	if ( $enable_share_button == 'yes' ) {
		$events_html .= $share_buttons; }

		$events_html .= '</div>';
		$events_html .= '<div class="ect-timeline-main-content">';
		$events_html .= '<div class="ect-timeline-date">';
		$events_html .= $event_schedule;
		$events_html .= '</div>';
		$events_html .= '<h2 class="content-title">' . wp_kses_post( $event_title ) . '</h2>';
		$events_html .= wp_kses_post( $event_content );
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= wp_kses_post( $venue_details_html );
	}
	if ( tribe_get_cost( $event_id, true ) ) {
		$events_html .= $event_cost;
	}
		$events_html .= '</div>';
		$events_html .= '      <div class="ect-lslist-event-detail">
	        <a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" title="' . get_the_title( $event_id ) . '" rel="bookmark">' . $events_more_info_text . '</a>
	        </div>';
		$events_html .= '</div>';
		$events_html .= '</div>';
	$i++;
} elseif ( $timeline_style == 'style-2' ) {
	if ( $events_date_header !== '' ) {
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . wp_kses_post( $events_date_header ) . '</div>
						</div>';
	}

	$events_html .= '<div id="post-' . esc_attr( $event_id ) . '" class="ect-timeline-post even ' . esc_attr( $event_type ) . ' ' . esc_attr( $timeline_style ) . ' ' . esc_attr( $time ) . '">';
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content even">';
	$events_html .= '<div class="ect-timeline-header">';
	if ( $ev_post_img ) {
		$events_html .= '<a class="timeline-ev-img" href="' . esc_url( tribe_get_event_link( $event_id ) ) . '"><img src= "' . $ev_post_img . '"/></a>';
	}
	$events_html .= $event_schedule;
	if ( $enable_share_button == 'yes' ) {
		$events_html .= $share_buttons; }
	$events_html .= '</div>';
	$events_html .= '<div class="ect-timeline-main-content">';
	$events_html .= '<h2 class="content-title">' . $event_title . '</h2>';
	$events_html .= $event_content;
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= wp_kses_post( $venue_details_html );
	}
	$events_html .= '</div>';
	$events_html .= '<div class="ect-timeline-footer">';
	if ( tribe_get_cost( $event_id, true ) ) {
		$events_html .= $event_cost;
	}
	$events_html .= '      <div class="ect-lslist-event-detail">
        <a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" title="' . get_the_title( $event_id ) . '" rel="bookmark">' . $events_more_info_text . '</a>
        </div></div>';
	$events_html .= '</div>';
	$events_html .= '</div>';
} else {
	if ( $events_date_header !== '' ) {
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . wp_kses_post( $events_date_header ) . '</div>
						</div>';
	}
	$events_html .= '<div id="post-' . esc_attr( $event_id ) . '" class="ect-timeline-post even ' . esc_attr( $event_type ) . ' ' . esc_attr( $timeline_style ) . ' ' . esc_attr( $time ) . '">';
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content even">';
	$events_html .= '<div class="ect-timeline-header">';
	if ( $ev_post_img ) {
		$events_html .= '<a class="timeline-ev-img" href="' . esc_url( tribe_get_event_link( $event_id ) ) . '"><img src= "' . $ev_post_img . '"/></a>';
	}
		$events_html .= $event_schedule;
	if ( $enable_share_button == 'yes' ) {
		$events_html .= $share_buttons;
	}
	$events_html .= '</div>';
	$events_html .= '<div class="ect-timeline-main-content">';
	$events_html .= '<h2 class="content-title">' . wp_kses_post( $event_title ) . '</h2>';
	$events_html .= wp_kses_post( $event_content );
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= wp_kses_post( $venue_details_html );
	}
	if ( tribe_get_cost( $event_id, true ) ) {
		$events_html .= '<div class="ect-timeline-footer">';
		$events_html .= $event_cost;
		$events_html .= '      <div class="ect-lslist-event-detail">
        <a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" title="' . get_the_title( $event_id ) . '" rel="bookmark">' . $events_more_info_text . '</a>
        </div></div>';
	} else {
		$events_html .= '<div class="ect-timeline-footer">';
		$events_html .= '<div class="ect-lslist-event-detail full-view">
        <a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" title="' . get_the_title( $event_id ) . '" rel="bookmark">' . $events_more_info_text . '</a>
        </div></div>';
	}
	$events_html .= '</div>';
	$events_html .= '</div>';
	$events_html .= '</div>';
}

