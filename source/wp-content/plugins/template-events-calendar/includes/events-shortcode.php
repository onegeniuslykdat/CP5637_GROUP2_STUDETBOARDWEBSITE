<?php

class EventsShortcode {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param array $options
	 */
	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	 /**
	  * Register all hooks
	  */

	public static function registers() {
		$thisPlugin = new self();
		/*** ECT main shortcode */
		add_shortcode( 'events-calendar-templates', array( $thisPlugin, 'ect_shortcodes' ) );

		require_once ECT_PLUGIN_DIR . 'includes/ect-styles.php';
		EctStyles::registers();
	}

		/*** ECT main shortcode */
	public function ect_shortcodes( $atts ) {
		if ( ! function_exists( 'tribe_get_events' ) ) {
			return;
		}
		 global $wp_query, $post;
		 global $more;
		 $more = false;

		 /*** Set shortcode default attributes */
		$attribute = shortcode_atts(
			apply_filters(
				'ect_shortcode_atts',
				array(
					'template'    => 'default',
					'style'       => 'style-1',
					'category'    => 'all',
					'date_format' => 'default',
					'start_date'  => '',
					'end_date'    => '',
					'time'        => 'future',
					'order'       => 'ASC',
					'limit'       => '10',
					'hide-venue'  => 'no',
					'event_tax'   => '',
					'month'       => '',
					'tags'        => '',
					'icons'       => '',
					'layout'      => '',
					'title'       => '',
					'design'      => '',
					'socialshare' => '',
				),
				$atts
			),
			$atts
		);

		$attribute = self::events_attr_filter( $attribute );

		 /*** Default var for later use */
		 $output              = '';
		 $events_html         = '';
		 $template            = isset( $attribute['template'] ) ? sanitize_text_field( $attribute['template'] ) : 'default';
		 $style               = isset( $attribute['style'] ) ? sanitize_text_field( $attribute['style'] ) : 'style-1';
		 $enable_share_button = isset( $attribute['socialshare'] ) ? sanitize_text_field( $attribute['socialshare'] ) : 'no';
		 $time                = isset( $attribute['time'] ) ? sanitize_text_field( $attribute['time'] ) : '';

		 /*** Load CSS styles based on template. */
		 EctStyles::ect_load_requried_assets( $template, $style );

		 /*** create query args based on shortcode attributes */
		if ( $attribute['category'] != 'all' ) {
			if ( $attribute['category'] ) {
				if ( strpos( $attribute['category'], ',' ) !== false ) {
					$attribute['category'] = explode( ',', $attribute['category'] );
					$attribute['category'] = array_map( 'trim', $attribute['category'] );
				} else {
					$attribute['category'] = $attribute['category'];
				}
				$attribute['event_tax'] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'tribe_events_cat',
						'field'    => 'name',
						'terms'    => $attribute['category'],
					),
					array(
						'taxonomy' => 'tribe_events_cat',
						'field'    => 'slug',
						'terms'    => $attribute['category'],
					),
				);
			}
		}

		 $prev_event_month  = '';
		 $prev_event_year   = '';
		 $meta_date_compare = '>=';
		 $attribute['key']  = '_EventStartDate';
		if ( $attribute['time'] == 'past' ) {
			$meta_date_compare = '<';
		} elseif ( $attribute['time'] == 'all' ) {
			$meta_date_compare = '';
		}
		 $attribute['key']       = '_EventStartDate';
		 $attribute['meta_date'] = '';
		 $meta_date_date         = '';
		if ( $meta_date_compare != '' ) {
			$meta_date_date         = current_time( 'Y-m-d H:i:s' );
			$attribute['key']       = '_EventStartDate';
			$attribute['meta_date'] = array(
				array(
					'key'     => '_EventEndDate',
					'value'   => $meta_date_date,
					'compare' => $meta_date_compare,
					'type'    => 'DATETIME',
				),
			);
		}
		/*** Fetch events based upon mentioned values */
		$ect_args = apply_filters(
			'ect_args_filter',
			array(
				'post_status'    => 'publish',
				'hide_upcoming'  => true,
				'posts_per_page' => $attribute['limit'],
				'tax_query'      => $attribute['event_tax'],
				'meta_key'       => $attribute['key'],
				'orderby'        => 'event_date',
				'order'          => $attribute['order'],
				'meta_query'     => $attribute['meta_date'],
			),
			$attribute,
			$meta_date_date,
			$meta_date_compare
		);

		if ( ! empty( $attribute['start_date'] ) ) {
			$ect_args['start_date'] = $attribute['start_date'];
		}
		if ( ! empty( $attribute['end_date'] ) ) {
			$ect_args['end_date'] = $attribute['end_date'];
		}
		  $all_events = tribe_get_events( $ect_args );
		 $i           = 0;
		if ( $all_events ) {
			$tect_settings         = get_option( 'ects_options' );
			$events_more_info_btn  = ! empty( $tect_settings['events_more_info'] ) ? sanitize_text_field( $tect_settings['events_more_info'] ) : esc_html__( 'Find out more', 'ect' );
			$events_more_info_text = sanitize_text_field( $events_more_info_btn );
			foreach ( $all_events as $post ) :
				setup_postdata( $post );
				$event_title        = '';
				$event_content      = '';
				$event_img          = '';
				$event_schedule     = '';
				$event_day          = '';
				$event_cost         = '';
				$event_venue        = '';
				$events_date_header = '';
				$no_events          = '';
				$event_type         = tribe( 'tec.featured_events' )->is_featured( $post->ID ) ? sanitize_text_field( 'ect-featured-event' ) : sanitize_text_field( 'ect-simple-event' );
				$event_id           = $post->ID;
				$share_buttons      = '';
				if ( $enable_share_button == 'yes' ) {
					wp_enqueue_script( 'ect-sharebutton', ECT_PLUGIN_URL . 'assets/js/ect-sharebutton.min.js', array( 'jquery' ), ECT_VERSION, true );
					wp_enqueue_style( 'ect-sharebutton-css', ECT_PLUGIN_URL . 'assets/css/ect-sharebutton.min.css', null, ECT_VERSION, 'all' );
					$share_buttons = ect_share_button( $event_id );
				}

				/*** Event date headers for timeline template */
				$show_headers = apply_filters( 'tribe_events_list_show_date_headers', true );
				if ( $show_headers ) {
					$event_year        = esc_html( tribe_get_start_date( $post, false, 'Y' ) );
					$event_month       = esc_html( tribe_get_start_date( $post, false, 'm' ) );
					$month_year_format = esc_html( tribe_get_date_option( 'monthAndYearFormat', 'M Y' ) );
					if ( $prev_event_month != $event_month || ( $prev_event_month == $event_month && $prev_event_year != $event_year ) ) {
						$prev_event_month    = $event_month;
						$prev_event_year     = $event_year;
						$date_header         = sprintf( "<span class='month-year-box'>%s</span>", esc_html( tribe_get_start_date( $post, false, 'M Y' ) ) );
						$events_date_header .= '<!-- Month / Year Headers -->';
						$events_date_header .= $date_header;
					}
				}

				/*** Event venue details */
				$venue_details_html  = '';
				$venue_details_html1 = '';
				$venue_details       = tribe_get_venue_details();
				/*** Setup an array of venue details for use later in the template */
				if ( $attribute['hide-venue'] != 'yes' ) {
					if ( $template == 'classic-list' || $template == 'modern-list' || $template == 'default' || $template == 'minimal-list' ) {
						$venue_details_html .= '<div class="ect-list-venue ' . esc_attr( $template ) . '-venue">';
					} else {
						$venue_details_html .= '<div class="' . esc_attr( $template ) . '-venue">';
					}

					if ( tribe_has_venue() ) :

						if ( $template === 'minimal-list' ) {
							$venue_details_html1 .= '<div class="' . $template . '-venue">';
							if ( isset( $venue_details['linked_name'] ) ) {
								$venue_details_html1 .= '<span class="ect-icon"><i class="ect-icon-location" aria-hidden="true"></i></span>';
								$venue_details_html1 .= '<span class="ect-venue-name">
                                ' . $venue_details['linked_name'] . '</span>
                                ';
								if ( tribe_get_map_link() ) {
									$venue_details_html1 .= '<span class="ect-google">' . tribe_get_map_link_html() . '</span>';
								}
							}
							$venue_details_html1 .= '</div>';
						} else {
							if ( ! empty( $venue_details['address'] ) && isset( $venue_details['linked_name'] ) ) {
								 $venue_details_html .= '<span class="ect-icon"><i class="ect-icon-location"></i></span>';
							}
							$venue_details_html .= '<!-- Event Venue Info -->
							<span class="ect-venue-details ect-address">
							<div>';
							$venue_details_html .= implode( ',', $venue_details );
							$venue_details_html .= '</div>';
							if ( tribe_get_map_link() ) {
								$venue_details_html .= '<span class="ect-google">' . wp_kses_post( tribe_get_map_link_html() ) . '</span>';
							}
							$venue_details_html .= '</span>';
						}
					endif;

					$venue_details_html .= '</div>';
				}

				/*** Event Cost */
				if ( tribe_get_cost( $event_id ) ) :
					$event_cost = '<!-- Event Ticket Price Info -->
                 <div class="ect-rate-area">
                 <span class="ect-icon"><i class="ect-icon-ticket"></i></span>
                 <span class="ect-rate">' . esc_html( tribe_get_cost( $event_id, true ) ) . '</span>
                 </div>';
			 endif;
				/*** event day */
				$event_day = '<span class="event-day">' . esc_html( tribe_get_start_date( $event_id, true, 'l' ) ) . '</span>';
				$ev_time   = $this->ect_tribe_event_time( $event_id, false );

				$event_schedule = ect_custom_date_formats( $attribute['date_format'], $template, $event_id, $ev_time );

				/*** Event title */
				$event_title = '<a class="ect-event-url" href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" rel="bookmark">' . wp_kses_post( get_the_title( $event_id ) ) . '</a>';

				/*** Event description - content */
				$event_content  = '<!-- Event Content --><div class="ect-event-content">';
				$event_content .= tribe_events_get_the_excerpt( $event_id, wp_kses_allowed_html( 'post' ) );
				$event_content .= '</div>';

				/*** Load templates based on shortcode */
				if ( in_array( $template, array( 'timeline', 'classic-timeline', 'timeline-view' ) ) ) {
					include ECT_PLUGIN_DIR . '/templates/timeline/timeline.php';
				} elseif ( in_array( $template, array( 'default', 'classic-list', 'modern-list' ) ) ) {
					include ECT_PLUGIN_DIR . '/templates/list/list.php';
				} elseif ( $template == 'minimal-list' ) {
					include ECT_PLUGIN_DIR . '/templates/minimal-list/minimal-list.php';
				} else {
					include ECT_PLUGIN_DIR . '/templates/list/list.php';
				}

			 endforeach;
			wp_reset_postdata();
		} else {
			$tect_settings       = get_option( 'ects_options' );
			$no_event_found_text = ! empty( $tect_settings['events_not_found'] ) ? sanitize_text_field( $tect_settings['events_not_found'] ) : '';

			$not_found_msz = '';
			if ( ! empty( $no_event_found_text ) ) {
				$not_found_msz = sanitize_text_field( $no_event_found_text );
			} else {
				$not_found_msz = '<div class="ect-no-events"><p>' . esc_html__( 'There are no upcoming events at this time.', 'ect' ) . '</p></div>';
			}
			$no_events = '<span class="ect-icon"><i class="ect-icon-bell"></i></span>' . $not_found_msz;
		}

		 $catCls = ( is_array( $attribute['category'] ) ) ? implode( ' ', $attribute['category'] ) : $attribute['category'];

		 /*** Generate output based on template */
		if ( $no_events ) {
			$output .= '<div id="ect-no-events"><p>' . sanitize_text_field( $no_events ) . '</p></div>';
		} else {
			if ( in_array( $template, array( 'timeline', 'classic-timeline', 'timeline-view' ) ) ) {
				if ( $template == 'timeline' ) {
					$style = 'style-1';
				} elseif ( $template == 'classic-timeline' ) {
					$style = 'style-2';
				}

				$output .= '<!=========Events Timeline Template ' . ECT_VERSION . '=========>';
				$output .= '<div id="event-timeline-wrapper" class="' . esc_attr( $catCls ) . ' ' . esc_attr( $style ) . '">';
				$output .= '<div class="cool-event-timeline">';
				$output .= $events_html;
				$output .= '</div></div>';
			} elseif ( $template == 'minimal-list' ) {
				$output .= '<!=========Events Static list Template ' . ECT_VERSION . '=========>';
				$output .= '<div id="ect-events-minimal-list-content">';
				$output .= '<div id="ect-minimal-list-wrp" class="ect-minimal-list-wrapper ' . esc_attr( $catCls ) . '">';
				$output .= $events_html;
				$output .= '</div></div>';
			} else {
				$output .= '<!=========Events list Template ' . ECT_VERSION . '=========>';
				$output .= '<div id="ect-events-list-content">';
				$output .= '<div id="list-wrp" class="ect-list-wrapper ' . esc_attr( $catCls ) . '">';
				$output .= $events_html;
				$output .= '</div></div>';
			}
		}

		 return $output;
	}



		/**
		 * Function to remove sql injection spacing to prevent sql statement to execute.
		 */
	public function events_attr_filter( $attr ) {
		$pattern    = '#[*\(\)\[\]{}"\'\\\\/;$]#';
		$attributes = array();
		foreach ( $attr as $key => $values ) {
				$value              = preg_replace( $pattern, '', $values );
				$value              = preg_replace( '/\s+/', '', $value );
				$value              = esc_html( $value );
				$attributes[ $key ] = $value;
		}
		return $attributes;
	}



		// get events dates and time
	public function ect_tribe_event_time( $event_id, $display = true ) {
		global $post;
		$event = $event_id;
		if ( tribe_event_is_multiday( $event ) ) { // multi-date event
			$start_date = tribe_get_start_date( $event, false );
			$end_date   = tribe_get_end_date( $event, false );
			if ( $display ) {
				printf( esc_html__( '%1$s - %2$s', 'ect' ), esc_html( $start_date ), esc_html( $end_date ) );

			} else {
				return sprintf( esc_html__( '%1$s - %2$s', 'ect' ), esc_html( $start_date ), esc_html( $end_date ) );

			}
		} elseif ( tribe_event_is_all_day( $event ) ) { // all day event
			if ( $display ) {
				printf( esc_html__( 'All day', 'the-events-calendar' ) );

			} else {
				return sprintf( esc_html__( 'All day', 'the-events-calendar' ) );

			}
		} else {
			$time_format = get_option( 'time_format' );
			$start_date  = tribe_get_start_date( $event, false, $time_format );
			$end_date    = tribe_get_end_date( $event, false, $time_format );
			if ( $start_date !== $end_date ) {
				if ( $display ) {
					printf( esc_html__( '%1$s - %2$s', 'ect' ), esc_html( $start_date ), esc_html( $end_date ) );

				} else {
					return sprintf( esc_html__( '%1$s - %2$s', 'ecct' ), esc_html( $start_date ), esc_html( $end_date ) );
				}
			} else {
				if ( $display ) {
					printf( esc_html__( '%s', 'ect' ), esc_html( $start_date ) );
				} else {
					return sprintf( esc_html__( '%s', 'ect' ), esc_html( $start_date ) );
				}
			}
		}
	}
}
