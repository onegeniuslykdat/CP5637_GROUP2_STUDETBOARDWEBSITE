<?php
$ev_day        = tribe_get_start_date( $event_id, false, 'd' );
$ev_month      = tribe_get_start_date( $event_id, false, 'M' );
$ev_full_month = tribe_get_start_date( $event_id, false, 'F' );
$ev_year       = tribe_get_start_date( $event_id, false, 'Y' );
$list_style    = $attribute['style'];

if ( $list_style == 'style-1' ) {

	$events_html .= '<div id="event-' . esc_attr( $event_id ) . '" class="ect-list-posts ' . esc_attr( $list_style ) . ' ' . esc_attr( $event_type ) . ' ' . esc_attr( $time ) . '">';
	$events_html .= '<div class="ect-event-date-tag"><div class="ect-event-datetimes">
			        <span class="ev-mo">' . esc_html( $ev_month ) . '</span>
			        <span class="ev-day">' . esc_html( $ev_day ) . '</span>
			        </div></div>';
	$events_html .= '<div class="ect-event-details">
    <div class="ect-events-title">' . wp_kses_post( $event_title ) . '</div>
                <div class="ect-event-datetime"><i class="ect-icon-clock"></i>
                <span class="ect-minimal-list-time">' . esc_html( $ev_time ) . '</span></div>
				';
	$events_html .= '</div></div>';

} elseif ( $list_style == 'style-2' ) {

		$events_html .= '<div id="event-' . esc_attr( $event_id ) . '" class="ect-list-posts ' . esc_attr( $list_style ) . ' ' . esc_attr( $event_type ) . ' ' . esc_attr( $time ) . '">';
		$events_html .= '<div class="ect-event-date ect-schedule-wrp ect-date-viewport">
                <span class="ect-date-viewport">' . esc_html( $ev_day ) . '</span>
                <span class="ect-month">' . esc_html( $ev_month ) . '</span>
                </div>';
		$events_html .= '<div class="ect-right-wrapper">';
		$events_html .= '<span class="ect-event-title">' . wp_kses_post( $event_title ) . '</span>';
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= $venue_details_html1;
	} else {
		$events_html .= '';
	}
		$events_html .= '</div></div>';

} else {


	$events_html                 .= '<div id="event-' . esc_attr( $event_id ) . '" class="ect-list-posts ' . esc_attr( $list_style ) . ' ' . esc_attr( $event_type ) . ' ' . esc_attr( $time ) . '">';
	$events_html                 .= '<div class="ect-left-wrapper">';
	$events_html                 .= '<div class="ect-event-dates"><div class="ect-event-datetimes">
                <span class="ev-day">' . esc_html( $ev_day ) . '</span>
                 <span class="ev-mo">' . esc_html( $ev_month ) . '</span>
             
                </div></div>';
	$events_html                 .= '</div>';
	$events_html                 .= '<div class="ect-right-wrapper">';
					$events_html .= '
                        <div class="ect-event-details"><span class="ect-minimal-list-time"><i class="ect-icon-clock"></i>
                    <span class="ect-minimal-list-time">' . esc_html( $ev_time ) . '</span></span></div>
                    <div class="ect-events-title">' . wp_kses_post( $event_title ) . '</div> ';
	$events_html                 .= '</div>';
	$events_html                 .= '</div>';

}

