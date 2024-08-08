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

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$get_temp_id           = get_option( 'tecset-single-page-id' );
$get_temp_cls          = epta_dynamic_class();
$tecset_custom_styles  = epta_custom_style();
$tecset_share_button   = epta_share_button( $event_id );
$tecset_date_format    = get_post_meta( $get_temp_id, 'tecset-date-format', true );
$tecset_event_schedule = epta_event_schedule( $event_id, $tecset_date_format );
$post_content          = \eptafunctions\epta_get_content( $event_id );
wp_enqueue_style( 'epta-bootstrap-css' );
wp_add_inline_style( 'epta-template2-css', $tecset_custom_styles );
wp_enqueue_style( 'epta-template2-css' );
wp_enqueue_style( 'epta-frontend-css' );
?>
<div id="epta-template" class="epta-row epta-<?php echo esc_attr($get_temp_cls); ?>">
	<section class="space-section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="epta-template" class="epta-row epta-<?php echo esc_attr($get_temp_cls); ?>">
						<div id="epta-tribe-events-content" class="tribe-events-single hentry">
							<div class="epta-events-single-right col-sm-9 col-sm-push-3">
								<div class="epta-featured-image-content">
									<!-- Event featured image, but exclude link -->
									<div class="epta-events-event-image">
										<?php echo wp_kses_post(tribe_event_featured_image( $event_id, 'full', false )); ?>
									</div>
									<!-- Event content -->
									<div class="epta-events-single-event-description">
										<?php echo wp_kses_post($post_content); ?>
									</div>
									<!-- .tribe-events-single-event-description -->
									<div class="epta-events-cal-links">
										<?php
										echo '<a class="tribe-events-gcal tribe-events-button" href="' . esc_url(Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() )) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '">+ ' . esc_html__( 'Google Calendar', 'the-events-calendar' ) . '</a>';
										echo '<a class="tribe-events-ical tribe-events-button" href="' . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '" >+ ' . esc_html__( 'iCal Export', 'the-events-calendar' ) . '</a>';
										?>
									</div><!-- .epta-events-cal-links -->
								</div> <!-- #post-x -->
							</div>

							<div class="epta-events-single-left col-sm-3 col-sm-pull-9">
								<div class="epta-events-cta clearfix">
									<div class="epta-events-cta-date">
										<?php echo wp_kses_post($tecset_event_schedule); ?>
									</div>
								</div>
								<!-- Event meta -->
								<div class="epta-events-meta-group epta-events-meta-group-details">
									<?php tribe_get_template_part( 'modules/meta/details' ); ?>
								</div>
								<?php
								if ( tribe_has_venue() ) {
									?>
								<div class="epta-events-meta-group epta-events-meta-group-venue">
									<?php tribe_get_template_part( 'modules/meta/venue' ); ?>
								</div>	
								<?php } ?>	
								<div class="epta-events-meta-group epta-events-meta-group-gmap clearfix">
									<div class="tribe-events-venue-map">
										<?php tribe_get_template_part( 'modules/meta/map' ); ?>
									</div>
								</div>
								<?php
								if ( tribe_has_organizer() ) {
									?>
								<div class="epta-events-meta-group epta-events-meta-group-schedule">
									<?php tribe_get_template_part( 'modules/meta/organizer' ); ?>     
								</div>
								<?php } ?>
								<div class="epta-events-meta-group epta-share-area">
									<?php echo $tecset_share_button; ?>
								</div>   
							</div>
							<div class="clearfix"></div>
							<!-- Event footer -->
							<div id="tribe-events-footer">
								<!-- Navigation -->
								<h3 class="tribe-events-visuallyhidden">Event Navigation</h3>
								<ul class="tribe-events-sub-nav">
									<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ); ?></li>
									<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ); ?></li>
								</ul>
								<!-- .tribe-events-sub-nav -->
							</div>
							<!-- #tribe-events-footer -->
						</div><!-- #tribe-events-content -->

						<div class="col-md-12">  
							<?php
							/**** Related Events ...START */
							$posts = epta_tribe_event_page_get_related_posts();
							if ( is_array( $posts ) && ! empty( $posts ) ) :
								?>
							<div class="epta-related-area">
								<h3 class="epta-related-head"><?php echo esc_html__( 'Related Events', 'epta' ); ?></h3>
								<div class="epta-row"> 
									<?php foreach ( $posts as $post ) : ?>
									<div class="col-sm-4">
										<div class="epta-related-box">
											<a href="<?php echo esc_url(tribe_get_event_link( $post )); ?>">
												<?php echo wp_kses_post(tribe_event_featured_image( $post->ID, 'full', false )); ?>
											</a>
											<div class="epta-related-title 
											<?php
											if ( tribe_event_featured_image( $post->ID, 'full', false ) == '' ) {
												echo 'no-image';}
											?>
											">
												<h4><a href="<?php echo esc_url(tribe_get_event_link( $post )); ?>"><?php echo esc_html(get_the_title( $post->ID )); ?></a></h4>
												<div class="epta-related-date"><?php echo wp_kses_post(tribe_events_event_schedule_details( $post->ID )); ?></div>
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>



