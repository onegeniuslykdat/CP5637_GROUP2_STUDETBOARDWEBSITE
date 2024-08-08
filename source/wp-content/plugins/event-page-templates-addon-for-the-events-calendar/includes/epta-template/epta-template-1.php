<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 */

namespace eptatemplate1;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$get_temp_id           = intval( get_option( 'tecset-single-page-id' ) );
$get_temp_cls          = \eptafunctions\epta_dynamic_class();
$tecset_custom_styles  = \eptafunctions\epta_custom_style();
$tecset_share_button   = \eptafunctions\epta_share_button( $event_id );
$tecset_date_format    = get_post_meta( $get_temp_id, 'tecset-date-format', true );
$tecset_event_schedule = \eptafunctions\epta_event_schedule( $event_id, $tecset_date_format );

$post_content      = \eptafunctions\epta_get_content( $event_id );
$tribe_all_events  = esc_url( tribe_get_events_link() );
$tecset_all_events = isset( $tecset_url ) && ! empty( $tecset_url ) ? $tecset_url : $tribe_all_events;


wp_add_inline_style( 'epta-frontend-css', $tecset_custom_styles );
wp_enqueue_style( 'epta-frontend-css' );
wp_enqueue_style( 'epta-bootstrap-css' );
wp_enqueue_script( 'epta-events-countdown-widget' );
?>


<div id="epta-template" class="epta-row epta-<?php echo esc_attr( $get_temp_cls ); ?>">
	<div class="epta-all-events col-md-12">
		<a href="<?php echo esc_url( $tecset_all_events ); ?>"><< <?php echo esc_html__( 'All Events', 'epta' ); ?></a>
	</div>
	<div class="col-md-8">

		<div class="epta-image-area">
			<?php
			if ( tribe_event_featured_image() ) {
				echo tribe_event_featured_image( $event_id, 'full', false );
				?>
				<div class="epta-title-date">
					<h2><?php sanitize_title_with_dashes( the_title() ); ?></h2>
					<?php echo wp_kses_post( $tecset_event_schedule ); ?>
					<div class="epta-light-bg"></div>
				</div>
				<?php
			} else {
				?>
				<div class="epta-title-date no-image">
					<h2><?php sanitize_title_with_dashes( the_title() ); ?></h2>
					<?php echo wp_kses_post( $tecset_event_schedule ); ?>
					<div class="epta-light-bg"></div>
				</div>
				<?php
			}
			?>
		</div>

		<?php do_action( 'tribe_events_single_event_before_the_content' ); ?>

		<div class="epta-content-area">
		   <?php
			if ( function_exists( 'tribe_get_custom_fields' ) ) {
				$additional_fields = tribe_get_custom_fields();
				if ( is_array( $additional_fields ) ) {
					foreach ( $additional_fields  as $key => $field ) {
						echo '<div class="epta-additional-content">' . wp_kses_post( $field ) . '</div>';
					}
				}
			}
			while ( have_posts() ) :
				the_post();
				?>
		<div class="tribe-events-single-event-description tribe-events-content">
				<?php
				the_content();
				?>
			 </div>
				<?php
	  endwhile;
			?>
		</div>

		<?php

		remove_action( 'tribe_events_single_event_after_the_content', array( tribe( 'tec.iCal' ), 'single_event_links' ) );
		do_action( 'tribe_events_single_event_after_the_content' );
		?>
		<?php do_action( 'tribe_events_single_event_before_the_meta' ); ?>
		<div class="epta-map-area">
			<?php tribe_get_template_part( 'modules/meta/map' ); ?>
		</div>

		<?php
		if ( class_exists( 'Tribe__Events__PRO__Main' ) ) {
			remove_action( 'tribe_events_single_event_after_the_meta', array( \Tribe__Events__PRO__Main::instance(), 'register_related_events_view' ) );
		}
		do_action( 'tribe_events_single_event_after_the_meta' );
		?>

		<!-- Share Buttons -->
		<div class="epta-share-area">
			<?php echo $tecset_share_button; ?>
		</div>




		<!-- END Tickets -->
	</div>


	<div class="col-md-4">
		<div class="epta-sidebar-area">
			<div class="epta-sidebar-box">
			<?php
			// Get the event start date.
			$start_date = tribe_get_start_date( get_the_ID(), false, 'Y/m/d H:i:s' );
			// Get the number of seconds remaining until the date in question.
			$seconds = strtotime( $start_date ) - current_time( 'timestamp' );
			if ( $seconds > 0 ) {
				?>
					<?php ob_start(); ?>
					<div class="epta-countdown-timer">
						<div class="epta-countdown-cell">
							<div class="epta-countdown-number">DD</div>
							<div class="epta-countdown-under"><?php esc_html_e( 'DAYS', 'epta' ); ?></div>
						</div>
						<div class="epta-countdown-cell">
							<div class="epta-countdown-number">HH</div>
							<div class="epta-countdown-under"><?php esc_html_e( 'HOURS', 'epta' ); ?></div>
						</div>
						<div class="epta-countdown-cell">
							<div class="epta-countdown-number">MM</div>
							<div class="epta-countdown-under"><?php esc_html_e( 'MIN', 'epta' ); ?></div>
						</div>
						<div class="epta-countdown-cell">
							<div class="epta-countdown-number tecset-countdown-last">SS</div>
							<div class="epta-countdown-under"><?php esc_html_e( 'SEC', 'epta' ); ?></div>
						</div>
					</div>
					<?php
					$hourformat = ob_get_clean();
					ob_start();
					?>
					<div class="epta-countdown-timer">
						<span class="epta-countdown-seconds"><?php echo wp_kses_post( $seconds ); ?></span>
						<span class="epta-countdown-format"><?php echo wp_kses_post( $hourformat ); ?></span>
					</div>
					<?php echo wp_kses_post( ob_get_clean() ); ?>
				<?php
			} else {
				$epta_notice = \eptafunctions\epta_get_passed_event_notice();
				if ( $epta_notice != '' ) {
					?>
					<!-- Notice -->
				<div class="epta-past-event-notice">
					<?php tribe_the_notices(); ?>
				</div>
					<?php
				}
			}
			?>
			</div>


			<!-- Event Details Box ...START-->
			<div class="epta-sidebar-box">
				<?php tribe_get_template_part( 'modules/meta/details' ); ?>
			</div>

			<!-- Event Details Box ...END-->


			<?php
			if ( tribe_has_venue() ) {
				?>
				<!-- Event Venue Box ...START-->
				<div class="epta-sidebar-box">
					<?php tribe_get_template_part( 'modules/meta/venue' ); ?>
				</div>
				<!-- Event Venue Box ...END-->
				<?php
			}


			if ( tribe_has_organizer() ) {
				?>
				<!-- Event Organizer Box ...START-->
				<div class="epta-sidebar-box">
					<?php tribe_get_template_part( 'modules/meta/organizer' ); ?>
				</div>
				<!-- Event Organizer Box ...END-->
				<?php
			}
			?>


			<!-- Add To Calendar Buttons ...START-->
			<div class="epta-sidebar-box">
				<div class="epta-addto-calendar">
					<?php
					echo '<a href="' . esc_url( \Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '">+ ' . esc_html__( 'Google Calendar', 'the-events-calendar' ) . '</a>';
					echo '<a href="' . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '" >+ ' . esc_html__( 'iCal Export', 'the-events-calendar' ) . '</a>';
					?>
				</div>
			</div>
			<!-- Add To Calendar Buttons ...END-->
			<div class="epta-sidebar-box">
				<div class="epta-registration-form">
					<?php
					if ( function_exists( 'rtec_the_registration_form' ) ) {
						rtec_the_registration_form();
					}
					?>
				</div>
			</div>
			<!-- Registration form end -->
		</div>
	</div>


	<div class="col-md-12">
		<?php
		/**** Related Events ...START */
		$posts = \eptasingleeventpage\epta_tribe_event_page_get_related_posts();
		if ( is_array( $posts ) && ! empty( $posts ) ) :
			?>
			<div class="epta-related-area">
				<h3 class="epta-related-head"><?php echo esc_html__( 'Related Events', 'epta' ); ?></h3>
				<div class="epta-row">
					<?php foreach ( $posts as $post ) : ?>
					<div class="col-sm-4">
						<div class="epta-related-box">
							<a href="<?php echo esc_url( tribe_get_event_link( $post ) ); ?>">
								<?php echo wp_kses_post( tribe_event_featured_image( $post->ID, 'full', false ) ); ?>
							</a>
							<div class="epta-related-title 
							<?php
							if ( tribe_event_featured_image( $post->ID, 'full', false ) == '' ) {
								echo 'no-image';}
							?>
							">
								<h4><a href="<?php echo esc_url( tribe_get_event_link( $post ) ); ?>"><?php echo esc_html( get_the_title( $post->ID ) ); ?></a></h4>
								<div class="epta-related-date"><?php echo wp_kses_post( tribe_events_event_schedule_details( $post->ID ) ); ?></div>
								<div class="epta-light-bg"></div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		endif;
		/**** Related Events ...END */
		?>
   

		<!-- Next/Prev ...START -->
		<div class="ept-next-prev" <?php tribe_events_the_header_attributes(); ?>>
			<ul>
				<li class="epta-prev"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ); ?></li>
				<li class="epta-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ); ?></li>
			</ul>
		</div>
		<!-- Next/Prev ...END -->
<!-- Comment -->
<?php
if ( get_post_type() == \Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) {
	comments_template();}
?>
<!-- comment end -->
	</div>
</div>
