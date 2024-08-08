<?php
/*
Plugin Name:Events Shortcodes For The Events Calendar
Plugin URI:https://eventscalendaraddons.com/plugin/events-shortcodes-pro/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugin_uri
Description:<a href="http://wordpress.org/plugins/the-events-calendar/">📅 The Events Calendar Addon</a> - Shortcodes to show The Events Calendar plugin events list on any page or post in different layouts.
Version:2.4.2
Requires at least:5.0
Tested up to:6.6
Requires PHP:7.2
Stable tag:trunk
Author:Cool Plugins
Author URI:https://coolplugins.net/about-us/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=author_uri
License URI:https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain:ect
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if ( ! defined( 'ECT_VERSION' ) ) {
	define( 'ECT_VERSION', '2.4.2' );
}
/*** Defined constent for later use */
define( 'ECT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ECT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/*** EventsCalendarTemplates main class by CoolPlugins.net */
if ( ! class_exists( 'EventsCalendarTemplates' ) ) {
	final class EventsCalendarTemplates {

		/**
		 * The unique instance of the plugin.
		 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {
		}

		// register all hooks
		public function registers() {
			if ( file_exists( plugin_dir_path( __DIR__ ) . 'the-events-calendar-templates-and-shortcode/the-events-calendar-templates-and-shortcode.php' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				if ( is_plugin_active( 'the-events-calendar-templates-and-shortcode/the-events-calendar-templates-and-shortcode.php' ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
					return;
				}
			}

			/*** Installation and uninstallation hooks */
			register_activation_hook( __FILE__, array( 'EventsCalendarTemplates', 'activate' ) );
			register_deactivation_hook( __FILE__, array( 'EventsCalendarTemplates', 'deactivate' ) );

			add_action( 'admin_init', array( self::$instance, 'ect_settings_migration' ) );
			add_action( 'admin_init', array( self::$instance, 'onInit' ) );
			add_action( 'activated_plugin', array( self::$instance, 'ect_plugin_redirection' ) );

			/*** Check The Event Calendar is installed or not */
			add_action( 'plugins_loaded', array( self::$instance, 'ect_check_event_calender_installed' ) );

			/*** Load required files */
			add_action( 'plugins_loaded', array( self::$instance, 'ect_load_files' ) );

			add_action( 'admin_enqueue_scripts', array( self::$instance, 'ect_tc_css' ) );
			/*** Template Setting Page Link */
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( self::$instance, 'ect_template_settings_page' ) );
			add_action( 'plugin_row_meta', array( self::$instance, 'ect_addMeta_Links' ), 10, 2 );
			foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
				add_action( "admin_head-$hook", array( self::$instance, 'ect_rest_url' ) );
			}

			/*** Include Gutenberg Block */
			require_once ECT_PLUGIN_DIR . 'admin/gutenberg-block/ect-block.php';

			/***Include Share Buttons*/
			require_once ECT_PLUGIN_DIR . '/includes/ect-share-functions.php';

		}



		/*** Load required files */
		public function ect_load_files() {
			load_plugin_textdomain( 'ect', false, basename( dirname( __FILE__ ) ) . '/languages/' );
			if ( class_exists( 'Tribe__Events__Main' ) or defined( 'Tribe__Events__Main::VERSION' ) ) {
				if ( defined( 'WPB_VC_VERSION' ) ) {
					require_once ECT_PLUGIN_DIR . 'admin/visual-composer/ect-class-vc.php';
				}
			}

			if ( is_admin() ) {
				/*** Plugin review notice file */
				require_once ECT_PLUGIN_DIR . '/admin/notices/admin-notices.php';
				require_once ECT_PLUGIN_DIR . 'admin/feedback/admin-feedback-form.php';

				require_once __DIR__ . '/admin/events-addon-page/events-addon-page.php';
				cool_plugins_events_addon_settings_page( 'the-events-calendar', 'cool-plugins-events-addon', '📅 Events Addons For The Events Calendar' );

				require_once ECT_PLUGIN_DIR . 'admin/codestar-framework/codestar-framework.php';
				require_once ECT_PLUGIN_DIR . 'admin/ect-codestar-settings.php';
				$settings_panel = new ECTSettings();

			}

			/*** Include helpers functions*/
			require_once ECT_PLUGIN_DIR . 'includes/ect-functions.php';

			 require_once ECT_PLUGIN_DIR . 'includes/events-shortcode.php';
			 EventsShortcode::registers();
			 require_once ECT_PLUGIN_DIR . 'admin/ect-event-shortcode.php';

		}

		public static function onInit() {
			if ( version_compare( get_option( 'ect-v' ), '2.4.0', '<' ) ) {
				ect_create_admin_notice(
					array(
						'id'              => 'ect-pro-setting-change',
						'message'         => wp_kses_post( __( '<strong>Major design update</strong> for <strong>Events Shortcodes</strong> plugin in version 2.4.0! Update or reset <a href=' . admin_url( 'admin.php?page=tribe_events-events-template-settings' ) . '>style settings</a> if you face any design issues.', 'ect' ) ),
						'review_interval' => 0,
					)
				);
			}

			if ( version_compare( get_option( 'ect-v' ), '1.8', '<' ) ) {
				ect_create_admin_notice(
					array(
						'id'              => 'ect-free-setting-migration',
						'message'         => wp_kses_post( __( '<strong>Important Update</strong>:- <strong>Events Shortcodes & Templates</strong> plugin has integrated new settings panel. Please save your settings and check events views.', 'ect' ) ),
						'review_interval' => 0,
					)
				);
			}
			if ( did_action( 'elementor/loaded' ) && ! class_exists( 'Events_Calendar_Addon' ) ) {
				ect_create_admin_notice(
					array(
						'id'              => 'ect-elementor-addon-notice',
						'message'         => wp_kses_post(
							__(
								'Hi! We checked that you are using <strong>Elementor Page Builder</strong>.
					<br/>We suggest you to try "<a target="_blank" href="https://eventscalendaraddons.com/plugin/events-widgets-pro/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=get_pro_ectbe&utm_content=elementor_notice"><strong>Events Widgets For Elementor</strong></a>," a new addon by <a target="_blank" href="https://coolplugins.net/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=elementor_notice">Cool Plugins</a>.
					<br/>It enables you to display <strong>The Events Calendar</strong> plugin events in Elementor pages.',
								'ect'
							)
						),
						'review_interval' => 3,
						'logo'            => ECT_PLUGIN_URL . 'assets/images/icon-events-widgets.svg',
					)
				);
			}
			/*** Plugin review notice file */
			ect_create_admin_notice(
				array(
					'id'              => 'ect_review_box',  // required and must be unique
					'slug'            => 'ect',      // required in case of review box
					'review'          => true,     // required and set to be true for review box
					'review_url'      => esc_url( 'https://wordpress.org/support/plugin/template-events-calendar/reviews/#new-post' ), // required
					'plugin_name'     => 'Events Shortcodes  Addon',    // required
					'logo'            => ECT_PLUGIN_URL . 'assets/images/icon-events-shortcodes.svg',    // optional: it will display logo
					'review_interval' => 3,                    // optional: this will display review notice
													  // after 5 days from the installation_time
														  // default is 3
				)
			);
		}

		public function shortcodes_submenu() {
			add_submenu_page( 'cool-plugins-events-addon', 'Shortcodes & Template', '<strong>Shortcodes & Template</strong>', 'manage_options', 'admin.php?page=tribe_events-events-template-settings', false, 15 );
		}

		/*** Check The Events calender is installled or not. If user has not installed yet then show notice */
		public function ect_check_event_calender_installed() {
			if ( ! class_exists( 'Tribe__Events__Main' ) or ! defined( 'Tribe__Events__Main::VERSION' ) ) {
				add_action( 'admin_notices', array( $this, 'Install_ECT_Notice' ) );
			}
		}
		public function Install_ECT_Notice() {

			if ( current_user_can( 'activate_plugins' ) ) {

				printf(
					'<div class="error CTEC_Msz"><p>' .
					esc_html( __( '%1$s %2$s', 'ebec' ) ),
					esc_html( __( 'In order to use this addon, Please first install the latest version of', 'ebec' ) ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url( 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true' ),
						esc_html( __( 'The Events Calendar', 'ebec' ) )
					) . '</p></div>'
				);

			}
		}

		/*** Admin side shortcode generator style CSS */
		public function ect_tc_css() {
			$current_screen = get_current_screen();
			$screen_name    = isset( $current_screen->base ) ? esc_html( $current_screen->base ) : '';
			if ( $screen_name == 'events-addons_page_tribe_events-events-template-settings' ) {
				wp_enqueue_script( 'ectcsf-codemirror', ECT_PLUGIN_URL . 'assets/ect-codemirror/js/codemirror.min.js', array( 'csf' ), ECT_VERSION, true );
				wp_enqueue_script( 'ectcsf-codemirror-loadmode', ECT_PLUGIN_URL . 'assets/ect-codemirror/js/loadmode.min.js', array( 'ectcsf-codemirror' ), ECT_VERSION, true );
				wp_enqueue_script( 'ectcsf-html-mixed', ECT_PLUGIN_URL . 'assets/ect-codemirror/js/ect-html-mixed-min.js', array( 'ectcsf-codemirror' ), ECT_VERSION, true );
				wp_enqueue_style( 'ectcsf-codemirror', ECT_PLUGIN_URL . 'assets/ect-codemirror/css/codemirror.min.css', array(), ECT_VERSION, 'all' );
				wp_enqueue_script( 'ect-show-pro-setting', ECT_PLUGIN_URL . 'assets/js/ect-show-pro-setting.js', array(), ECT_VERSION, 'all' );
			}
			wp_enqueue_style( 'sg-btn-css', plugins_url( 'assets/css/shortcode-generator.css', __FILE__ ) );
		}
		/*** Add links in plugin install list */
		public function ect_template_settings_page( $links ) {
			$links[] = '<a style="font-weight:bold" href="' . esc_url( get_admin_url( null, 'admin.php?page=tribe_events-events-template-settings' ) ) . '">Shortcodes Settings</a>';
			// $links[] = '<a  style="font-weight:bold" href="https://eventscalendartemplates.com/" target="_blank">View Demos</a>';
			$plugin_visit_website = 'https://eventscalendaraddons.com/plugin/events-shortcodes-pro/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugins_list';
			$links[]              = '<a  style="font-weight:bold" href="' . esc_url( $plugin_visit_website ) . '" target="_blank">' . __( 'Get Pro', 'ect' ) . '</a>';
			return $links;
		}
		/**
		 * Add meta links to the Plugins list page.
		 *
		 * @param array  $links The current action links.
		 * @param string $file  The plugin to see if we are on Event Single Page.
		 *
		 * @return array The modified action links array.
		 */
		public function ect_addMeta_Links( $links, $file ) {
			if ( strpos( $file, basename( __FILE__ ) ) ) {
				$ectanchor   = esc_html__( 'Video Tutorials', 'ect' );
				$ectvideourl = 'https://eventscalendaraddons.com/go/ect-video-tutorial/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=video_tutorial&utm_content=plugins_list';
				$links[]     = '<a href="' . esc_url( $ectvideourl ) . '" target="_blank">' . $ectanchor . '</a>';
			}

			return $links;
		}

		// set settings on plugin activation
		public static function activate() {
			update_option( 'ect-v', ECT_VERSION );
			update_option( 'ect-type', 'FREE' );
			update_option( 'ect-free-installDate', date( 'Y-m-d h:i:s' ) );
			update_option( 'ect-ratingDiv', 'no' );
		}
		public static function deactivate() {
			delete_option( 'settings_migration_status' );
			delete_option( 'ect-v' );
			delete_option( 'ect-type' );
			delete_option( 'ect-free-installDate' );
			delete_option( 'ect-ratingDiv' );

		}


		public function ect_rest_url() {
			?>
			<!-- TinyMCE Shortcode Plugin -->
			<script type='text/javascript'>
			var ectRestUrl='<?php echo esc_url( get_rest_url( null, '/tribe/events/v1/' ) ); ?>'
			</script>
			<!-- TinyMCE Shortcode Plugin -->
			<?php
		}

		/*
			Old settings migration
		*/

			// old titan settings panel fields data
		function get_titan_settings() {
			$new_settings = array();
			if ( get_option( 'ect_options' ) != false ) {
				$titan_raw_data = get_option( 'ect_options' );
				if ( is_serialized( $titan_raw_data ) ) {
					$titan_settings = array_filter( maybe_unserialize( $titan_raw_data ) );
					if ( is_array( $titan_settings ) ) {
						foreach ( $titan_settings as $key => $val ) {
							$new_settings[ $key ] = maybe_unserialize( $val );
						}
					}
				}
				return $new_settings;
			} else {
				return false;
			}
		}

		function ect_settings_migration() {
			if ( version_compare( get_option( 'ect-v' ), '1.8', '>' ) ) {
				return;
			}
			if ( get_option( 'settings_migration_status' ) ) {
				return;
			}

				$old_settings = $this->get_titan_settings();
			if ( $old_settings == false ) {
				return;
			}
			if ( is_array( $old_settings ) ) {

				$req_settings = array(
					'font-family',
					'font-size',
					'font-weight',
					'font-style',
					'line-height',
					'letter-spacing',
					'text-transform',
					'color',
					'font-type',
				);
				$webSafeFonts = array(
					'Arial, Helvetica, sans-serif'         => 'Arial',
					'"Arial Black", Gadget, sans-serif'    => 'Arial Black',
					'"Comic Sans MS", cursive, sans-serif' => 'Comic Sans MS',
					'"Courier New", Courier, monospace'    => 'Courier New',
					'Georgia, serif'                       => 'Geogia',
					'Impact, Charcoal, sans-serif'         => 'Impact',
					'"Lucida Console", Monaco, monospace'  => 'Lucida Console',
					'"Lucida Sans Unicode", "Lucida Grande", sans-serif' => 'Lucida Sans Unicode',
					'"Palatino Linotype", "Book Antiqua", Palatino, serif' => 'Palatino Linotype',
					'Tahoma, Geneva, sans-serif'           => 'Tahoma',
					'"Times New Roman", Times, serif'      => 'Times New Roman',
					'"Trebuchet MS", Helvetica, sans-serif' => 'Trebuchet MS',
					'Verdana, Geneva, sans-serif'          => 'Verdana',
				);
				$old_font_arr = array_flip( $webSafeFonts );

				$new_settings = array();
				foreach ( $old_settings as $key => $field_val ) {
					if ( is_array( $field_val ) ) {
						foreach ( $field_val as $index => $val ) {
							if ( in_array( $index, $req_settings ) ) {
								if ( $index == 'font-type' ) {
									$index = 'type';
								} elseif ( $index == 'font-size' ) {
									$val = str_replace( 'px', '', $val );
								} elseif ( $index == 'line-height' ) {
									$val = str_replace( 'em', '', $val );
								} elseif ( $index == 'letter-spacing' ) {
									$val = str_replace( 'em', '', $val );
								} elseif ( $index == 'font-family' ) {
									$found = array_search( $val, $old_font_arr );
									$val   = $found ? $found : $val;

								}

										$new_settings[ $key ][ $index ] = $val;
							}
						}
						$new_settings[ $key ]['line_height_unit'] = 'em';
						$new_settings[ $key ]['unit']             = 'px';
						$new_settings[ $key ]['subset']           = '';
						$new_settings[ $key ]['text-align']       = '';
						$new_settings[ $key ]['font-variant']     = '';

					} else {
						$new_settings[ $key ] = $field_val;
					}
				}
				update_option( 'ects_options', $new_settings );
				update_option( 'settings_migration_status', 'done' );
				delete_option( 'ect_options' );
			}

		}
		public function ect_plugin_redirection( $plugin ) {
			if ( plugin_basename( __FILE__ ) === $plugin ) {
				exit( wp_redirect( admin_url( 'admin.php?page=tribe_events-events-template-settings#tab=get-started' ) ) );
			}
		}
	}

}
/*** EventsCalendarTemplates main class - END */


/*** THANKS - CoolPlugins.net ) */
$ect = EventsCalendarTemplates::get_instance();
$ect->registers();

