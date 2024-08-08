<?php
/**
 * Get Template Slug
 */
namespace eptafunctions;

function epta_dynamic_class() {
	$tecset_pageid    = get_option( 'tec_tribe_single_event_page' );
	$get_select_temp  = get_post_meta( $tecset_pageid, 'epta-select-temp', true );
	$tecset_page_slug = ( ! empty( $get_select_temp ) ) ? $get_select_temp : 'template-1';
	return $tecset_page_slug;
}
function epta_get_passed_event_notice() {
	ob_start();
	tribe_the_notices();
	return ob_get_clean();
}
function epta_get_content( $more_link_text = '(more...)', $stripteaser = 0, $more_file = '' ) {

		$content = get_the_content( $more_link_text, $stripteaser, $more_file );

	$ept_content         = apply_filters( 'the_excerpt', $content );
		$ept_get_content = str_replace( ']]>', ']]&gt;', $ept_content );

			 return $ept_get_content;

}
function epta_custom_style() {
	$get_temp_id                    = get_option( 'tec_tribe_single_event_page' );
	$tecset_get_primary_color       = get_post_meta( $get_temp_id, 'epta-primary-color', true );
	$tecset_set_primary_color       = ! empty( $tecset_get_primary_color ) ? $tecset_get_primary_color : '#222222';
	$tecset_get_secondary_color     = get_post_meta( $get_temp_id, 'epta-secondary-alternate-color', true );
	$tecset_set_secondary_color     = ! empty( $tecset_get_secondary_color ) ? $tecset_get_secondary_color : '#cccccc';
	$tecset_primary_alternate_color = get_post_meta( $get_temp_id, 'epta-alternate-primary-color', true );
	$tecset_set_alternate_color     = ! empty( $tecset_primary_alternate_color ) ? $tecset_primary_alternate_color : '#ffffff';

	$tecset_p_color = "
	#epta-template.epta-template-1 .epta-light-bg,
	#epta-template.epta-template-1 .epta-countdown-cell,
	#epta-template.epta-template-1 .epta-sidebar-box h2.tribe-events-single-section-title,
	#epta-template.epta-template-1 .epta-addto-calendar a{
		background-color:{$tecset_set_primary_color};
	}
	#epta-template.epta-template-1 .epta-registration-form #rtec .rtec-register-button{
		background-color:{$tecset_set_primary_color};
	}
	#epta-template.epta-template-1 .epta-title-date h2,
	#epta-template.epta-template-1 .epta-title-date .tecset-date,
	#epta-template.epta-template-1 .epta-countdown-cell,
	#epta-template.epta-template-1 .epta-sidebar-box h2.tribe-events-single-section-title,
	#epta-template.epta-template-1 .epta-addto-calendar a,
	#epta-template.epta-template-1 .epta-related-title h4,
	#epta-template.epta-template-1 .epta-related-title h4 a,
	#epta-template.epta-template-1 .epta-related-date{
		color:{$tecset_set_alternate_color};
	}
	#epta-template.epta-template-1 .epta-registration-form #rtec .rtec-register-button{
		color:{$tecset_set_alternate_color};
	}
	#epta-template.epta-template-1 .epta-sidebar-area,
	#epta-template.epta-template-1 .epta-map-area .tribe-events-venue-map {
		background-color:{$tecset_set_secondary_color};
	}
	#epta-template.epta-template-1 .epta-share-area a {
		color:{$tecset_set_primary_color};
	}
	#epta-template.epta-template-1 .epta-map-area .tribe-events-venue-map {
		border-color:{$tecset_set_secondary_color};
	}
	.epta-template-2 #epta-tribe-events-content.tribe-events-single .epta-events-cta .epta-events-cta-date .tecset-ev-day{
		color:{$tecset_set_alternate_color};
	}
	.epta-template-2 #epta-tribe-events-content.tribe-events-single .epta-events-cta .epta-events-cta-date .tecset-ev-mo{
		color:{$tecset_set_alternate_color};
	}
	.epta-template-2 #epta-tribe-events-content.tribe-events-single .epta-events-cta .epta-events-cta-date .tecset-ev-yr{
		color:{$tecset_set_alternate_color};
	}
	#epta-template.epta-template-2 .epta-related-title h4 a {
		color:{$tecset_set_alternate_color};
	}
	#epta-template.epta-template-2 .epta-related-date{
		color:{$tecset_set_alternate_color};
	}
	.epta-template-2 .epta-events-single-left {
		background:{$tecset_set_secondary_color};
	}
	.epta-template-2 .epta-light-bg{
		background:{$tecset_set_primary_color};
	}
	.epta-template-2 .epta-share-area a {
		color: {$tecset_set_primary_color};
	}
	.epta-template-2 h2.tribe-events-single-section-title{
		background:{$tecset_set_primary_color};
		color:{$tecset_set_alternate_color};

	}
	.epta-template-2 h3.tecset-share-title {
		color:{$tecset_set_alternate_color};
		background:{$tecset_set_primary_color};

	}
	.epta-template-2 .epta-events-meta-group.epta-events-meta-group-details{
		border-top:2px solid{$tecset_set_primary_color};
	}
	.epta-template-2 .epta-events-meta-group.epta-events-meta-group-venue{
		border-top:2px solid{$tecset_set_primary_color};
	}
	.epta-template-2 #epta-tribe-events-content.tribe-events-single .epta-events-meta-group-schedule{
		border-top:2px solid{$tecset_set_primary_color};
	}
	.epta-template-2 .epta-share-area{
		border-top:2px solid{$tecset_set_primary_color};
	}
	#tribe-events .epta-template-2 .tribe-events-button{
		color:{$tecset_set_alternate_color} !important;
		background:{$tecset_set_primary_color} !important;
	}
	.epta-template-2 li.tribe-events-nav-previous a{
		color:{$tecset_set_alternate_color};
	}
	.epta-template-2 li.tribe-events-nav-next a{
		color:{$tecset_set_alternate_color};
	}
	.epta-template-2 #epta-tribe-events-content.tribe-events-single .epta-events-single-left{
		border-right:2px solid{$tecset_set_primary_color};
	}
	.epta-template-2 h3.epta-related-head{
		background:{$tecset_set_primary_color};
		color:{$tecset_set_alternate_color};
	}";

	return $tecset_p_color;
}
/**
 * This file is used to share events.
 *
 * @package the-events-calendar-templates-and-shortcode/includes
 */

function epta_share_button( $event_id ) {

	wp_enqueue_script( 'tecset-sharebutton', EPTA_PLUGIN_URL . 'assets/js/epta-sharebutton.js', array( 'jquery' ), null, true );
	wp_enqueue_style( 'tecset-customicon-css', EPTA_PLUGIN_URL . 'assets/css/epta-custom-icon.css', null, null, 'all' );
	$tecset_sharecontent = '';
	$tecset_get_url      = urlencode( get_permalink( $event_id ) );

	$tecset_gettitle     = htmlspecialchars( urlencode( html_entity_decode( get_the_title( $event_id ), ENT_COMPAT, 'UTF-8' ) ), ENT_COMPAT, 'UTF-8' );
	$tecset_getthumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $event_id ), 'full' );
	$subject             = str_replace( '+', ' ', $tecset_gettitle );
	// Construct sharing URL
		$tecset_twitterURL    = 'https://twitter.com/intent/tweet?text=' . $tecset_gettitle . '&amp;url=' . $tecset_get_url . '';
		$tecset_whatsappURL   = 'https://wa.me/?text=' . $tecset_gettitle . ' ' . $tecset_get_url;
		$tecset_facebookurl   = 'https://www.facebook.com/sharer/sharer.php?u=' . $tecset_get_url . '';
		$tecset_emailUrl      = 'mailto:?Subject=' . $subject . '&Body=' . $tecset_get_url . '';
		$tecset_sharecontent .= '<h3 class="tecset-share-title">' . __( 'Share This Event', 'epta' ) . '</h3>';
		$tecset_sharecontent .= '<a class="tecset-share-link" href="' . esc_url( $tecset_facebookurl ) . '" target="_blank" title="Facebook" aria-haspopup="true"><i class="ect-icon-facebook"></i></a>';
		$tecset_sharecontent .= '<a class="tecset-share-link" href="' . esc_url( $tecset_twitterURL ) . '" target="_blank" title="Twitter" aria-haspopup="true"><i class="ect-icon-twitter"></i></a>';
		// $tecset_sharecontent .= '<a class="ect-share-link" href="'.$ect_linkedinUrl.'" target="_blank" title="Linkedin" aria-haspopup="true"><i class="ect-icon-linkedin"></i></a>';
		$tecset_sharecontent .= '<a class="tecset-email" href="' . esc_url( $tecset_emailUrl ) . ' "title="Email" aria-haspopup="true"><i class="ect-icon-mail"></i></a>';
		$tecset_sharecontent .= '<a class="tecset-share-link" href="' . esc_url( $tecset_whatsappURL ) . '" target="_blank" title="WhatsApp" aria-haspopup="true"><i class="ect-icon-whatsapp"></i></a>';
		return $tecset_sharecontent;
}
// generate events dates html
function epta_event_schedule( $event_id, $tecset_date_format ) {
	/*Date Format START*/

	$tecset_ev_time        = epta_tribe_event_time( $event_id, false );
	$tecset_event_schedule = '';

	// $tecset_ev_time=$this->ect_tribe_event_time($event_id,false);
	if ( $tecset_date_format == 'DM' ) {
			$tecset_event_schedule = '<div class="tecset-date"  itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'M' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'MD' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'FD' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'DF' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'F' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'FD,Y' ) {
			$tecset_event_schedule = '<div class="tecset-date"  itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'MD,Y' ) {
			$tecset_event_schedule = '<div class="tecset-date"  itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'MD,YT' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-time"><span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> ' . $tecset_ev_time . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-time">(' . tribe_get_start_date( $event_id, false, 'g:i A' ) . ')</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . ', </span>
				<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-time">(' . tribe_get_end_date( $event_id, false, 'g:i A' ) . ')</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'full' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
					<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
					<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
					<span class="tecset-ev-time">
					<span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> ' . $tecset_ev_time . '</span>
					</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
					<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
					<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
					<span class="tecset-ev-time">(' . tribe_get_start_date( $event_id, false, 'g:i A' ) . ')</span>
					<span class="tecset-ev-blank"> - </span>
					<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
					<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'F' ) . '</span>
					<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
					<span class="tecset-ev-time">(' . tribe_get_end_date( $event_id, false, 'g:i A' ) . ')</span>
					</div>';
		}
				$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'dFY' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'F' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} elseif ( $tecset_date_format == 'dMY' ) {
			$tecset_event_schedule = '<div class="tecset-date" itemprop="startDate" content="' . tribe_get_start_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
		if ( ! tribe_event_is_multiday( $event_id ) ) {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		} else {
			$tecset_event_schedule .= '<span class="tecset-ev-day">' . tribe_get_start_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_start_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_start_date( $event_id, false, 'Y' ) . '</span>
				<span class="tecset-ev-blank"> - </span>
				<span class="tecset-ev-day">' . tribe_get_end_date( $event_id, false, 'd' ) . '</span>
				<span class="tecset-ev-mo">' . tribe_get_end_date( $event_id, false, 'M' ) . '</span>
				<span class="tecset-ev-yr">' . tribe_get_end_date( $event_id, false, 'Y' ) . '</span>
				</div>';
		}
			$tecset_event_schedule .= '<meta itemprop="endDate" content="' . tribe_get_end_date( $event_id, false, 'Y-m-dTg:i' ) . '">';
	} else {
			 $tecset_event_schedule = '<div class="tecset-date">' . tribe_events_event_schedule_details( $event_id ) . '</div>';
	}
	/*Date Format END*/
	return $tecset_event_schedule;
}
// grab events time for later use
function epta_tribe_event_time( $post_id, $display = true ) {
			$event = $post_id;
	if ( tribe_event_is_all_day( $event ) ) { // all day event
		if ( $display ) {
					esc_html_e( 'All day', 'the-events-calendar' );
		} else {
						return esc_html__( 'All day', 'the-events-calendar' );
		}
	} elseif ( tribe_event_is_multiday( $event ) ) { // multi-date event
			$tecset_start_date = tribe_get_start_date( $event, false, false );
			$tecset_end_date   = tribe_get_end_date( $event, false, false );
		if ( $display ) {
			printf( '%1$s - %2$s', esc_html( $tecset_start_date ), esc_html( $tecset_end_date ) );
		} else {

						return sprintf( '%1$s - %2$s', esc_html( $tecset_start_date ), esc_html( $tecset_end_date ) );
		}
	} else {
			$time_format       = get_option( 'time_format' );
			$tecset_start_date = tribe_get_start_date( $event, false, $time_format );
			$tecset_end_date   = tribe_get_end_date( $event, false, $time_format );
		if ( $tecset_start_date !== $tecset_end_date ) {
			if ( $display ) {
				printf( '%1$s - %2$s', esc_html( $tecset_start_date ), esc_html( $tecset_end_date ) );
			} else {
				return sprintf( '%1$s - %2$s', esc_html( $tecset_start_date ), esc_html( $tecset_end_date ) );
			}
		} else {
			if ( $display ) {
					printf( '%s', esc_html( $tecset_start_date ) );
			} else {
				return sprintf( '%s', esc_html( $tecset_start_date ) );
			}
		}
	}
}


function ect_get_url_by_slug( $slug ) {
	$page_url_id   = get_page_by_path( $slug );
	$page_url_link = get_permalink( $page_url_id );
	return $page_url_link;
}
