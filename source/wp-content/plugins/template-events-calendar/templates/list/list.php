<?php
$ev_day     = tribe_get_start_date($event_id, false, 'd' );
$ev_month   = tribe_get_start_date($event_id, false, 'M' );
$list_style = $attribute['style'];

if( $template == "modern-list" ) {
	$list_style = 'style-2';
}
else if( $template == "classic-list" ) {
	$list_style = 'style-3';
}

$ev_post_img = ect_get_event_image($event_id,$size='large');

/*** Default List Style 3 */
if(($style=="style-3" && $template=="default") || $template=="classic-list") {
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post '.esc_attr($list_style).' '.esc_attr($event_type).' '.esc_attr( $time ).'">';
	$bg_styles="background-image:url('$ev_post_img');background-size:cover;background-position:bottom center;";
	$events_html.='<div class="ect-list-post-left"><a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ect-list-img-link">
	<div class="ect-list-img" style="'.esc_attr( $bg_styles ).'"><div class="ect-list-date">'.wp_kses_post($event_schedule).'</div></div>
	</a></div>';      	
	$events_html.='<div class="ect-list-post-right"> 
	<div class="ect-list-post-right-table">
	<div class="ect-list-description">
				<h2 class="ect-list-title">'.wp_kses_post($event_title).'</h2>
				<div class="ev-smalltime"><span class="ect-icon"><i class="ect-icon-clock"></i></span><span class="cls-list-time">'.esc_attr( $ev_time ).'</span></div>
				';	
	if (tribe_has_venue($event_id)) {
		$events_html.=$venue_details_html;
	}
	else{
		$events_html.='';
	}
	$events_html.=$event_content;
	$events_html.=$event_cost;
	$events_html.= '<a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ect-events-read-more" rel="bookmark">' . esc_html__( $events_more_info_text, 'ect' ) . '</a>';
	$events_html.='</div></div></div>';
	if ( $enable_share_button == 'yes' ) {
	$events_html.='<div class="ect-clslist-event-details">';
	$events_html.= $share_buttons;
	$events_html.='</div>';
	}
	$events_html.='</div>';
}


/*** Default List Style 2 */
else if (($style=="style-2" && $template=="default") || $template=="modern-list") {
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post '.esc_attr($list_style).' '.esc_attr($event_type).' '.esc_attr( $time ).'">';
	$event_single_link = esc_url( tribe_get_event_link($event_id));
	$event_title_att   = get_the_title($event_id);
	$bg_styles="background-image:url('$ev_post_img');background-size:cover;background-position:bottom center;";

	$events_html .= '<div class="ect-list-post-left">';
    $events_html .= '<a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ect-list-img-link">';
    $events_html .= '<div class="ect-list-img" style="' . esc_attr( $bg_styles ) . '">';
    $events_html .= '</div></a><!-- left-post close -->';
	$events_html .= wp_kses_post( $share_buttons );
    $events_html .= '</div>';

	$events_html.='<div class="ect-list-post-right">
				<div class="ect-list-post-right-table">
				<div class="ect-list-description">
				<h2 class="ect-list-title">'.wp_kses_post($event_title).'</h2>';
	$events_html.=$event_content;
	$events_html.=$event_cost;	
	$events_html.= '<a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ect-events-read-more" rel="bookmark">' . esc_html__( $events_more_info_text, 'ect' ) . '</a>';
	$events_html.='</div>';

	$events_html .='<div class="modern-list-right-side">
				<div class="ect-list-date">'.wp_kses_post($event_schedule).'</div>';
				if (tribe_has_venue($event_id)) {
					$events_html.=$venue_details_html;
				}
				else{
					$events_html.='';
				}
	$events_html.='</div>
				</div>
				</div><!-- right-wrapper close -->
				</div><!-- event-loop-end -->';
}
/*** Default List Style 1 */
else{
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post style-1 '.esc_attr($event_type).' '.esc_attr( $time ).'">';

	$bg_styles="background-image:url('$ev_post_img');background-size:cover;";
	$events_html.='<div class="ect-list-post-left ">
				<div class="ect-list-img" style="'.esc_attr( $bg_styles ).'">';
	$events_html.='<a href="'.esc_url( tribe_get_event_link($event_id)).'" alt="'.esc_attr(get_the_title($event_id)).'" rel="bookmark">';
	$events_html .='<div class="ect-list-date">'.wp_kses_post($event_schedule).'</div></a>';
	$events_html.= wp_kses_post($share_buttons);
	$events_html.='</div></div><!-- left-post close -->';
	$events_html.='<div class="ect-list-post-right">
				<div class="ect-list-post-right-table">';

	if ( $attribute['hide-venue'] != 'yes' ) {
		if ( tribe_has_venue( $event_id ) ) {
			$events_html .= '<div class="ect-list-description">';
		} else {
			$events_html .= '<div class="ect-list-description" style="width:100%;">';
		}
		} else {
			$events_html .= '<div class="ect-list-description" style="width:100%;">';
		}
				
	$events_html.='<h2 class="ect-list-title">'.wp_kses_post($event_title).'</h2>';
	$events_html.=wp_kses_post($event_content);
	$events_html.=wp_kses_post($event_cost);
	$events_html.= '<a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ect-events-read-more" rel="bookmark">' . esc_html__( $events_more_info_text, 'ect' ) . '</a>';
	$events_html.='</div>';
	if (tribe_has_venue($event_id)) {
		
		$events_html.=$venue_details_html;
	}else{
		$events_html.='';
	}
	
	$events_html.='</div></div><!-- right-wrapper close -->';
	$events_html.='</div><!-- event-loop-end -->';
}