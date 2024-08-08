<?php
/*
Plugin Name: Event Single Page Builder For The Event Calendar
Plugin URI: https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugin_uri
Description: <a href="http://wordpress.org/plugins/the-events-calendar/"><b>📅 The Events Calendar Addon</b></a> - Design The Event Calendar plugin event single page template with custom colors and fonts.
Version: 1.7.3
Author:  Cool Plugins
Author URI: https://coolplugins.net/about-us/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=author_uri
License:GPL2
Text Domain:epta
*/

namespace EventPageTemplatesAddon;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
if ( ! defined( 'EPTA_PLUGIN_CURRENT_VERSION' ) ) {
	define( 'EPTA_PLUGIN_CURRENT_VERSION', '1.7.3' );
}
define( 'EPTA_PLUGIN_FILE', __FILE__ );
define( 'EPTA_PLUGIN_URL', plugin_dir_url( EPTA_PLUGIN_FILE ) );
define( 'EPTA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/**
 * Main Class
 */
if ( ! class_exists( 'EventPageTemplatesAddon' ) ) {
	class EventPageTemplatesAddon {

		/**
		 *  Construct the plugin object
		 */
		public function __construct() {
			 register_activation_hook( __FILE__, array( $this, 'epta_single_page_builder_activate' ) );
			 add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'epta_add_action_links' ) );
			 add_action( 'plugin_row_meta', array( $this, 'eptaaddMetaLinks' ), 10, 2 );
			 add_action( 'elementor/widgets/register', array( $this, 'epta_on_widgets_registered' ) );
			if ( is_admin() ) {
				require_once __DIR__ . '/admin/events-addon-page/events-addon-page.php';
				cool_plugins_events_addon_settings_page( 'the-events-calendar', 'cool-plugins-events-addon', '📅 Events Addons For The Events Calendar' );
				add_action( 'custom_menu_order', array( $this, 'epta_submenu_order' ) );
			}
			add_action( 'plugins_loaded', array( $this, 'epta_init' ) );
			// add_action( 'plugins_loaded', array( $this, 'epta_page_include_files' ) );

			$this->epta_page_include_files();
			add_action( 'init', array( $this, 'epta_notice_required_plugin' ) );

			add_action( 'init', array( $this, 'epta_add_single_event_page_details' ), 15 );
			$this->epta_add_actions();
			add_action( 'cmb2_admin_init', array( $this, 'cmb2_tecsbp_metaboxes' ) );
			add_action( 'save_post_epta', array( $this, 'save_event_meta_data' ), 1, 2 );
		}

		/**
		 * Function to blacklist Tec widgets
		 */
		public function epta_on_widgets_registered() {

			$post_type = get_post_type();
			global $tec_registered_widgets;
			$tec_registered_widgets = array(
				'tec_events_elementor_widget_event_categories',
				'tec_events_elementor_widget_event_calendar_link',
				'tec_events_elementor_widget_event_cost',
				'tec_events_elementor_widget_event_datetime',
				'tec_events_elementor_widget_event_export',
				'tec_events_elementor_widget_event_image',
				'tec_events_elementor_widget_event_navigation',
				'tec_events_elementor_widget_event_organizer',
				'tec_events_elementor_widget_event_status',
				'tec_events_elementor_widget_event_tags',
				'tec_events_elementor_widget_event_title',
				'tec_events_elementor_widget_event_venue',
				'tec_events_elementor_widget_event_website',
				'tec_events_elementor_widget_event_related',
				'tec_events_elementor_widget_event_venue',
				'tec_events_elementor_widget_event_organizer',
				'tec_events_elementor_widget_event_additional_fields',
				'tec_elementor_widget_event_single_legacy',
				'tec_elementor_widget_countdown',
				'tec_elementor_widget_events_list_widget',
				'tec_elementor_widget_events_view',
			);

			if ( 'tribe_events' == $post_type ) {
				add_filter(
					'elementor/editor/localize_settings',
					function( $settings ) {
						global $tec_registered_widgets;
						foreach ( $tec_registered_widgets as $widget_name ) {
							$settings['initial_document']['widgets'][ $widget_name ]['show_in_panel'] = false;
						}
						return $settings;
					},
					99
				);
				?>
					<style>
						.tec-events-elementor-template-selection-helper {
							display: none !important;
						}
					</style>
				<?php
			}

		}

		/**
		 *  Function to order the submenu of events addon
		 */
		public function epta_submenu_order() {
			global $submenu;
			$newSubmenu = array();
			foreach ( $submenu as $menuName => $menuItems ) {
				if ( 'cool-plugins-events-addon' === $menuName ) {
					$test                  = isset( $menuItems[0] ) ? $menuItems[0] : '';
					$test1                 = isset( $menuItems[1] ) ? $menuItems[1] : '';
					$activePlugin          = 'events-widgets-pro/events-widgets-pro.php';
					$isPluginActive        = is_plugin_active( $activePlugin );
					$isSingleEventTemplate = ( $isPluginActive ? $test1[0] : $test[0] ) == 'Event Page Template';

					if ( $isSingleEventTemplate ) {
						$total      = count( $menuItems );
						$newSubmenu = array();

						for ( $i = ( $isPluginActive ? 2 : 1 ); $i < $total; $i++ ) {
							$newSubmenu[ $i - ( $isPluginActive ? 1 : 0 ) ] = $menuItems[ $i ];
						}

						$newSubmenu[ $total - ( $isPluginActive ? 1 : 0 ) ] = $menuItems[0];
						if ( $isPluginActive ) {
							$newSubmenu[ $total ] = $menuItems[1];
						}
						$submenu['cool-plugins-events-addon'] = $newSubmenu;
						break;
					}
				}
			}
		}


		/**
		 * Add Actions
		 * function to create new column on template list table
		 *
		 * @since 1.6.6
		 *
		 * @access private
		 */
		private function epta_add_actions() {
			add_action( 'init', array( $this, 'epta_post_type' ), 5 );
			add_filter( 'manage_epta_posts_columns', array( $this, 'epta_add_new_columns' ) );
			add_action( 'manage_epta_posts_custom_column', array( $this, 'epta_manage_columns' ), 10, 2 );
		}
		function epta_add_new_columns() {
			$new_columns             = array();
			$new_columns['cb']       = '<input type="checkbox" />';
			$new_columns['title']    = __( 'Title', 'epta' );
			$new_columns['apply_on'] = __( 'Applied On', 'epta' );
			$new_columns['date']     = __( 'Date', 'epta' );
			return $new_columns;
		}
		function epta_manage_columns( $column, $post_id ) {
			 $text       = '';
			$value       = '';
			$specifc_val = '';
			if ( 'apply_on' == $column ) {
				// $get_temp_id =  get_option('tec_tribe_single_event_page');
				$epta_apply_on = get_post_meta( $post_id, 'epta-apply-on', true );
				if ( ! empty( $epta_apply_on ) ) {
					if ( $epta_apply_on == 'specific-event' ) {
						$text  = __( 'Specific Event', 'epta' );
						$value = get_post_meta( $post_id, 'epta-specific-event', true );
					} elseif ( $epta_apply_on == 'specific-tag' ) {
						$text  = __( 'Specific Tag', 'epta' );
						$value = get_post_meta( $post_id, 'epta-tag', true );
					} elseif ( $epta_apply_on == 'specific-cate' ) {
						$text  = __( 'Specific Category', 'epta' );
						$value = get_post_meta( $post_id, 'epta-categoery', true );
					} elseif ( $epta_apply_on == 'all-event' ) {
						$text = __( 'All Event', 'epta' );
					}
					if ( ! empty( $value ) ) {
						$specifc_val = implode( ',', $value );
					}
					$set_value = ( $text . ':-' . $specifc_val );
					if ( $set_value == ':-' ) {
						echo 'N/A';
					} else {
						echo $set_value;
					}
				} else {
					echo 'N/A';
				}
			}

		}
		/**
		 * Add meta links to the Plugins list page.
		 *
		 * @param array  $links The current action links.
		 * @param string $file  The plugin to see if we are on Event Single Page.
		 *
		 * @return array The modified action links array.
		 */
		public function eptaaddMetaLinks( $links, $file ) {
			if ( strpos( $file, basename( __FILE__ ) ) ) {
				$eptaanchor   = esc_html__( 'Video Tutorials', 'epta' );
				$eptavideourl = 'https://eventscalendaraddons.com/docs/events-single-page-builder-pro/video-tutorials/?utm_source=espbp_plugin&utm_mediu[%E2%80%A6]ide&utm_campaign=video_tutorial&utm_content=plugins_list';
				$links[]      = '<a href="' . esc_url( $eptavideourl ) . '" target="_blank">' . $eptaanchor . '</a>';
			}

			return $links;
		}
		/**
		 *  Function to create notice for promotion of Event Single Page Builder Pro
		 */
		public function epta_pro_promotion_notice() {
			$pluginList = get_option( 'active_plugins' );
			$plugin     = 'elementor/elementor.php';
			$plugin1    = 'event-page-templates-addon-for-the-events-calendar/the-events-calendar-event-details-page-templates.php';
			if ( in_array( $plugin, $pluginList ) && in_array( $plugin1, $pluginList ) ) {
				$epta_get_post_type = $this->epta_get_post_type_page();
				if ( $epta_get_post_type == 'epta' || $epta_get_post_type == 'cool-plugins-events-addon' ) {
					epta_create_admin_notice(
						array(
							'id'              => 'epta-new-plugin',
							'message'         => 'Hi! It appears that you are currently using <strong>Elementor</strong>. We suggest you to try <a href="https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=elementor_notice" target="_blank"><strong>Event Single Page Builder Pro</strong></a> for designing event single page templates in Elementor.',
							'review_interval' => 0,
						)
					);
				}
			}
			epta_create_admin_notice(
				array(
					'id'              => 'epta-review-box',  // required and must be unique
					'slug'            => 'epta',      // required in case of review box
					'review'          => true,     // required and set to be true for review box
					'review_url'      => esc_url( 'https://wordpress.org/support/plugin/events-widgets-for-elementor-and-the-events-calendar/reviews/?filter=5#new-post' ), // required
					'plugin_name'     => 'Event Single Page Builder For The Event Calendar',    // required
					'logo'            => EPTA_PLUGIN_URL . 'assets/images/icon-event-single-page-builder.svg',    // optional: it will display logo
					'review_interval' => 0,                    // optional: this will display review notice
													   // after 5 days from the installation_time
													   // default is 3
				)
			);
		}
		// custom links for add widgets in all plugins section
		public function epta_add_action_links( $links ) {
			  $epta_settings         = admin_url() . 'edit.php?post_type=epta';
			   $plugin_visit_website = 'https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugins_list';
			   $links[]              = '<a  style="font-weight:bold" href="' . esc_url( $epta_settings ) . '" target="_self">' . __( 'Template', 'epta' ) . '</a>';
			   $links[]              = '<a  style="font-weight:bold" href="' . esc_url( $plugin_visit_website ) . '" target="_blank">' . __( 'Get Pro', 'epta' ) . '</a>';
			   return $links;

		}
		// function epta_admin_menu() {
		// add_submenu_page( 'cool-plugins-events-addon', 'Single Events Template Builder', '<strong>Single Page Template</strong>', 'manage_options', 'edit.php?post_type=epta', false, 30 );
		// add_submenu_page( 'cool-plugins-events-addon', 'Single Events Template Builder', '↳ Edit Template', 'manage_options', 'edit.php?post_type=epta', false, 31 );
		// }
		/*
		|--------------------------------------------------------------------------
		| Code you want to run when all other plugins loaded.
		|--------------------------------------------------------------------------
		 */
		public function epta_init() {

			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'epta_pro_promotion_notice' ) );
				require __DIR__ . '/admin/class-admin-notice.php';

				require_once __DIR__ . '/admin/feedback/admin-feedback-form.php';
				// require_once __DIR__ . '/includes/epta-feedback-notice.php';
				// new \eptaFeedbackNotice\eptaFeedbackNotice();
			}

			load_plugin_textdomain( 'epta', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		public function save_event_meta_data( $post_id, $post ) {
			// handle the case when the custom post is quick edited
			// otherwise all custom meta fields are cleared out
			if ( isset( $_POST['_inline_edit'] ) && wp_verify_nonce( sanitize_text_field( $_POST['_inline_edit'] ), 'inlineeditnonce' ) ) {
				return;
			}

			if ( empty( $post_id ) || empty( $post ) ) {
				return;
			}

			// Dont' save meta boxes for revisions or autosaves
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Check user has permission to edit
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			// if (!empty($_POST['epta-apply-on'])) {
			if ( ! empty( filter_var( $_POST['epta-apply-on'], FILTER_SANITIZE_STRING ) ) ) {
				update_option( 'tec_tribe_single_event_page', $post_id );
			}

			// }
		}

		/**
		 * This function is used to display notice if the required plugin is not activated.
		 */
		public function epta_notice_required_plugin() {
			if ( file_exists( plugin_dir_path( __DIR__ ) . 'event-single-page-builder-pro/event-single-page-builder-pro.php' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				if ( is_plugin_active( 'event-single-page-builder-pro/event-single-page-builder-pro.php' ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
				}
			}
			 // load_plugin_textdomain('ect', false, basename(dirname(__FILE__)) . '/languages/');
			if ( ! class_exists( 'Tribe__Events__Main' ) or ! defined( 'Tribe__Events__Main::VERSION' ) ) {
				add_action( 'admin_notices', array( $this, 'epta_Install_ECT_Notice' ) );
			}

		}

		// notice for installation TEC parent plugin installation
		public function epta_Install_ECT_Notice() {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url         = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';
				$title       = __( 'The Events Calendar', 'tribe-events-ical-importer' );
				$plugin_info = get_plugin_data( __FILE__, true, true );
				printf(
					'<div class="error CTEC_Msz"><p>' .
					esc_html( __( '%1$s %2$s', 'tecc1' ) ),
					esc_html( __( 'In order to use our plugin, Please first install the latest version of', 'tecc1' ) ),
					sprintf(
						'<a href="%s" class="thickbox" title="%s">%s</a>',
						esc_url( $url ),
						esc_html( $title ),
						esc_html( $title )
					) . '</p></div>'
				);
				deactivate_plugins( __FILE__ );
			}
		}
		/*
		|--------------------------------------------------------------------------
		| generating page with shortcode for single event page
		|--------------------------------------------------------------------------
		*/
		public function epta_add_single_event_page_details() {
			$tecset_post_data = array(
				'post_title'  => 'Single Event Template',
				'post_type'   => 'epta',
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
			);

			$single_page_id = intval( get_option( 'tecset-single-page-id' ) );

			if ( 'publish' === get_post_status( $single_page_id ) && get_post_type( $single_page_id ) == 'epta' ) {

			} else {
				$post_id = wp_insert_post( $tecset_post_data );
				update_option( 'tecset-single-page-id', $post_id );
			}
		}

		/*
		|--------------------------------------------------------------------------
		|   on plugin activation hook adding page
		|--------------------------------------------------------------------------
		 */
		public function epta_single_page_builder_activate() {
			update_option( 'tecset-installDate', gmdate( 'Y-m-d h:i:s' ) );
			update_option( 'tecset-ratingDiv', 'no' );
		}

		// Register Custom Post Type
		public function epta_post_type() {
			$labels = array(
				'name'                  => _x( 'Event Page Template', 'Post Type General Name', 'tecspb2' ),
				'singular_name'         => _x( 'Event Page Template', 'Post Type Singular Name', 'tecspb2' ),
				'menu_name'             => __( 'Event Page Templates', 'tecspb2' ),
				'name_admin_bar'        => __( 'Event Page Templates', 'tecspb2' ),
				'archives'              => __( 'Item Archives', 'tecspb2' ),
				'attributes'            => __( 'Item Attributes', 'tecspb2' ),
				'parent_item_colon'     => __( 'Parent Item:', 'tecspb2' ),
				'all_items'             => __( 'Event Page Template', 'tecspb2' ),

				'update_item'           => __( 'Update Item', 'tecspb2' ),
				'view_item'             => __( 'View Item', 'tecspb2' ),
				'view_items'            => __( 'View Items', 'tecspb2' ),
				'search_items'          => __( 'Search Item', 'tecspb2' ),
				'not_found'             => __( 'Not found', 'tecspb2' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'tecspb2' ),
				'featured_image'        => __( 'Featured Image', 'tecspb2' ),
				'set_featured_image'    => __( 'Set featured image', 'tecspb2' ),
				'remove_featured_image' => __( 'Remove featured image', 'tecspb2' ),
				'use_featured_image'    => __( 'Use as featured image', 'tecspb2' ),
				'insert_into_item'      => __( 'Insert into item', 'tecspb2' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'tecspb2' ),
				'items_list'            => __( 'Items list', 'tecspb2' ),
				'items_list_navigation' => __( 'Items list navigation', 'tecspb2' ),
				'filter_items_list'     => __( 'Filter items list', 'tecspb2' ),
			);
			$args   = array(
				'label'               => __( '', 'tecspb2' ),
				'description'         => __( 'Post Type Description', 'tecspb2' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'taxonomies'          => array( '' ),
				'hierarchical'        => true,
				'public'              => false,  // it's not public, it shouldn't have it's own permalink, and so on
				'show_ui'             => true,
				'show_in_menu'        => 'cool-plugins-events-addon', // 'edit.php?post_type=tribe_events',
				'menu_position'       => 5,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,  // it shouldn't have archive page
				'rewrite'             => false,  // it shouldn't have rewrite rules
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				// 'menu_icon'           => EPTA_PLUGIN_URL.'/assets/images/pb-icon.png',
				'capability_type'     => 'post',
				'capabilities'        => array(
					'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
				),
				'map_meta_cap'        => true,

			);
			register_post_type( 'epta', $args );
		}
		/**
		 * Define the metabox and field configurations.
		 */
		public function cmb2_tecsbp_metaboxes() {
			$prefix = 'epta-';
			if ( ! class_exists( 'Tribe__Events__Main' ) ) {
				return;
			} else {
				require_once EPTA_PLUGIN_DIR . 'includes/epta-settings.php';
			}

		}



		/**
		 * Include required files
		 */
		public function epta_page_include_files() {
			 require_once EPTA_PLUGIN_DIR . 'admin/cmb2/init.php';

			require_once EPTA_PLUGIN_DIR . 'includes/epta-filter.php';

			if ( is_admin() ) {
				$tecset_get_post_type = $this->epta_get_post_type_page();
				if ( $tecset_get_post_type == 'epta' ) {
					require_once EPTA_PLUGIN_DIR . 'admin/cmb2/cmb2-conditionals.php';
					require_once EPTA_PLUGIN_DIR . 'admin/cmb2/cmb-field-select2/cmb-field-select2.php';
				}

				add_action( 'admin_enqueue_scripts', array( $this, 'epta_tc_css' ) );
				add_action( 'manage_posts_extra_tablenav', array( $this, 'add_pro_button' ) );

			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'epta_register_assets' ) );
			}
		}
		/*
		check admin side post type page
		*/
		public function epta_get_post_type_page() {
			global $post, $typenow, $current_screen;

			if ( $post && $post->post_type ) {
				return $post->post_type;
			} elseif ( $typenow ) {
				return $typenow;
			} elseif ( $current_screen && $current_screen->post_type ) {
				return $current_screen->post_type;
			} elseif ( isset( $_REQUEST['page'] ) ) {
				return sanitize_key( $_REQUEST['page'] );
			} elseif ( isset( $_REQUEST['post_type'] ) ) {
				   return sanitize_key( $_REQUEST['post_type'] );
			} elseif ( isset( $_REQUEST['post'] ) ) {
				return get_post_type( sanitize_text_field( $_REQUEST['post'] ) );
			}
			return null;
		}

		/**
		 * Get Pro button on templates page
		 */
		function add_pro_button( $which ) {
			if ( $which == 'top' ) {
					$epta_get_post_type = $this->epta_get_post_type_page();
				if ( $epta_get_post_type != 'epta' ) {
					return false;
				}
				?>
				<a class="like_it_btn button button-primary" target="_blank"
				href="https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=top_button">
					Get Pro ⇗</a>
				<?php
			}
		}
		/**
		 * Admin side css
		 */
		public function epta_tc_css() {
			wp_enqueue_style( 'tecset-sg-icon', plugins_url( '/assets/css/epta-admin.css', __FILE__ ) );
			wp_enqueue_script( 'tecset-select-temp', plugins_url( '/assets/js/epta-template-preview.js', __FILE__ ) );
		}
		/**
		 * register assets
		 */
		public function epta_register_assets() {
			wp_register_style( 'epta-frontend-css', EPTA_PLUGIN_URL . 'assets/css/epta-style.css', null, null, 'all' );
			wp_register_style( 'epta-template2-css', EPTA_PLUGIN_URL . 'assets/css/epta-template2-style.css', null, null, 'all' );
			wp_register_style( 'epta-bootstrap-css', EPTA_PLUGIN_URL . 'assets/css/epta-bootstrap.css', null, null, 'all' );
			 $add_customcss = $this->epta_custom_css();
			wp_add_inline_style( 'epta-frontend-css', $add_customcss );
			wp_add_inline_style( 'epta-template2-css', $add_customcss );
			wp_register_script( 'epta-events-countdown-widget', EPTA_PLUGIN_URL . 'assets/js/epta-widget-countdown.js', array( 'jquery' ), '', true );
		}
		/**
		 * Dynamic style
		 */
		function epta_custom_css() {
			// global $post;
			$tecset_pageid     = get_option( 'tec_tribe_single_event_page' );
			$tecset_custom_css = get_post_meta( $tecset_pageid, 'epta-custom-css', true );
			return $tecset_custom_css;
		}

	}//end class
}
new EventPageTemplatesAddon();
