<?php

class EctStyles {


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
	 *
	 * @param array $options
	 */
	public function __construct() {     }

	 /**
	  * Register all hooks
	  */

	public static function registers() {
		$thisPlugin = new self();
		/*** Enqueued script and styles */
		// add_action('wp_enqueue_scripts', array($thisPlugin, 'ect_styles'));
		$thisPlugin::load_files();

	}

	/*** Register CSS style assets */
	public static function ect_styles() {
		wp_register_style( 'ect-common-styles', ECT_PLUGIN_URL . 'assets/css/ect-common-styles.min.css', null, ECT_VERSION, 'all' );
		wp_register_style( 'ect-timeline-styles', ECT_PLUGIN_URL . 'assets/css/ect-timeline.min.css', null, ECT_VERSION, 'all' );
		wp_register_style( 'ect-list-styles', ECT_PLUGIN_URL . 'assets/css/ect-list-view.min.css', null, ECT_VERSION, 'all' );
		wp_register_style( 'ect-minimal-list-styles', ECT_PLUGIN_URL . 'assets/css/ect-minimal-list-view.css', null, ECT_VERSION, 'all' );
		// scripts
		wp_register_script( 'ect-sharebutton', ECT_PLUGIN_URL . 'assets/js/ect-sharebutton.min.js', array( 'jquery' ), ECT_VERSION, true );
		wp_register_style( 'ect-sharebutton-css', ECT_PLUGIN_URL . 'assets/css/ect-sharebutton.min.css', null, ECT_VERSION, 'all' );
	}
	public static function load_files() {

			// Inside ect-tinycolor folder exists darken,lighten color.
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/TinyColor.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/util.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/Traits/Convert.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/Traits/Names.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/Traits/Combination.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/Traits/Modification.php';
			require_once ECT_PLUGIN_DIR . 'includes/ect-tinycolor/Color.php';
	}


	/*** Load CSS styles based on template. */
	public static function ect_load_requried_assets( $template, $style ) {
		wp_enqueue_style( 'ect-common-styles', ECT_PLUGIN_URL . 'assets/css/ect-common-styles.min.css', null, ECT_VERSION, 'all' );
		$thisPlugin   = new self();
		$custom_style = $thisPlugin::ect_custom_styles( $template, $style );
		if ( in_array( $template, array( 'timeline', 'classic-timeline', 'timeline-view' ) ) ) {
			wp_enqueue_style( 'ect-timeline-styles', ECT_PLUGIN_URL . 'assets/css/ect-timeline.min.css', null, ECT_VERSION, 'all' );
			wp_add_inline_style( 'ect-timeline-styles', $custom_style );
		} elseif ( $template == 'minimal-list' ) {

			wp_enqueue_style( 'ect-minimal-list-styles', ECT_PLUGIN_URL . 'assets/css/ect-minimal-list-view.css', null, ECT_VERSION, 'all' );
			wp_add_inline_style( 'ect-minimal-list-styles', $custom_style );
		} else {

			wp_enqueue_style( 'ect-list-styles', ECT_PLUGIN_URL . 'assets/css/ect-list-view.min.css', null, ECT_VERSION, 'all' );
			wp_add_inline_style( 'ect-list-styles', $custom_style );
		}
	}
	public static function get_typeo_output( $settings ) {
		$output        = '';
		$important     = '';
		$font_family   = ( ! empty( $settings['font-family'] ) ) ? $settings['font-family'] : '';
		$backup_family = ( ! empty( $settings['backup-font-family'] ) ) ? ', ' . $settings['backup-font-family'] : '';
		if ( $font_family ) {
			$output .= 'font-family:"' . $font_family . '"' . $backup_family . $important . ';';
		}
		// Common font properties
		$properties = array(
			'color',
			'font-weight',
			'font-style',
			'font-variant',
			'text-align',
			'text-transform',
			'text-decoration',
		);

		foreach ( $properties as $property ) {
			if ( isset( $settings[ $property ] ) && $settings[ $property ] !== '' ) {
				$output .= $property . ':' . $settings[ $property ] . $important . ';';
			}
		}
		$properties = array(
			'font-size',
			'line-height',
			'letter-spacing',
			'word-spacing',
		);

		$unit = ( ! empty( $settings['unit'] ) ) ? $settings['unit'] : 'px';

		$line_height_unit = ( ! empty( $settings['line_height_unit'] ) ) ? $settings['line_height_unit'] : 'em';
		foreach ( $properties as $property ) {
			if ( isset( $settings[ $property ] ) && $settings[ $property ] !== '' ) {
				$unit    = ( $property === 'line-height' ) ? $line_height_unit : $unit;
				$output .= $property . ':' . $settings[ $property ] . $unit . $important . ';';
			}
		}
			return $output;
	}
	public static function ect_hex2rgba( $color, $opacity = false ) {

		$default = 'rgb(0,0,0)';

		// Return default if no color provided
		if ( empty( $color ) ) {
			  return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

			// Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}

			// Convert hexadec to rgb
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

			// Return rgb(a) color string
			return $output;
	}
		/** This function is used to apply custom styles/typography settings */
	public static function ect_custom_styles( $template, $style ) {
		 $thisPlugin    = new self();
		$ect_output_css = '';

		$options         = get_option( 'ects_options' );
		$all_saved_ff    = array();
		$custom_css      = ! empty( $options['custom_css'] ) ? $options['custom_css'] : '';
		$main_skin_color = ! empty( $options['main_skin_color'] ) ? $options['main_skin_color'] : '#5bbd8a';
		if ( empty( $options['main_skin_alternate_color'] ) ) {
			$ect_title_styles1         = ! empty( $options['ect_title_styles'] ) ? $options['ect_title_styles'] : '';
			$main_skin_alternate_color = $ect_title_styles1['color'];
		} else {
			$main_skin_alternate_color = ! empty( $options['main_skin_alternate_color'] ) ? $options['main_skin_alternate_color'] : '#ffffff';
		}
		$featured_event_skin_color = ! empty( $options['featured_event_skin_color'] ) ? $options['featured_event_skin_color'] : '#008cff';
		$featured_event_font_color = ! empty( $options['featured_event_font_color'] ) ? $options['featured_event_font_color'] : '#ffffff';
		$event_desc_bg_color       = ! empty( $options['event_desc_bg_color'] ) ? $options['event_desc_bg_color'] : '#ffffff';
		$title_styles              = $thisPlugin::get_typeo_output( ! empty( $options['ect_title_styles'] ) ? $options['ect_title_styles'] : '' );
		$ect_title_styles          = ! empty( $options['ect_title_styles'] ) ? $options['ect_title_styles'] : '';
		$ect_title_color           = ! empty( $ect_title_styles['color'] ) ? $ect_title_styles['color'] : '#383838';
		$ect_title_font_famiily    = ! empty( $ect_title_styles['font-family'] ) ? $ect_title_styles['font-family'] : '';
		$ect_title_font_size       = ! empty( $ect_title_styles['font-size'] ) ? $ect_title_styles['font-size'] : '18';
		$ect_desc_styles           = $thisPlugin::get_typeo_output( ! empty( $options['ect_desc_styles'] ) ? $options['ect_desc_styles'] : '' );
		$ect_venue_styles          = $thisPlugin::get_typeo_output( ! empty( $options['ect_desc_venue'] ) ? $options['ect_desc_venue'] : '' );
		$ect_date_style            = $thisPlugin::get_typeo_output( ! empty( $options['ect_dates_styles'] ) ? $options['ect_dates_styles'] : '' );
		$ect_date_style            = $thisPlugin::get_typeo_output( $options['ect_dates_styles'] );
		// Fetch Description Typograpy
		$ect_desc_style        = ! empty( $options['ect_desc_styles'] ) ? $options['ect_desc_styles'] : '';
		$ect_desc_color        = ! empty( $ect_desc_style['color'] ) ? $ect_desc_style['color'] : '#a5a5a5';
		$ect_desc_font_famiily = ! empty( $ect_desc_style['font-family'] ) ? $ect_desc_style['font-family'] : 'Open Sans';
		// Fetch venue Typography
		 $ect_venue_style       = ! empty( $options['ect_desc_venue'] ) ? $options['ect_desc_venue'] : '';
		$ect_venue_font_famiily = ! empty( $ect_venue_style['font-family'] ) ? $ect_venue_style['font-family'] : 'Open Sans';
		$ect_venue_font_size    = ! empty( $ect_venue_style['font-size'] ) ? $ect_venue_style['font-size'] : '15';
		$venue_font_size        = $ect_venue_font_size + $ect_venue_font_size / 3 . 'px';
		$ect_venue_color        = ! empty( $ect_venue_style['color'] ) ? $ect_venue_style['color'] : '#a5a5a5';
		// Fetch Date Typography
		$ect_date_styles      = ! empty( $options['ect_dates_styles'] ) ? $options['ect_dates_styles'] : '';
		$ect_date_font_family = ! empty( $ect_date_styles['font-family'] ) ? $ect_date_styles['font-family'] : 'Monda';
		$ect_date_color       = ! empty( $ect_date_styles['color'] ) ? $ect_date_styles['color'] : '#ffffff';
		$ect_date_font_weight = ! empty( $ect_date_styles['font-weight'] ) ? $ect_date_styles['font-weight'] : 'bold';
		$ect_date_font_style  = ! empty( $ect_date_styles['font-style'] ) ? $ect_date_styles['font-style'] : '';
		$ect_date_line_height = ! empty( $ect_date_styles['line-height'] ) ? $ect_date_styles['line-height'] : '1';

		$all_saved_ff['date_family'] = str_replace( ' ', '+', $ect_date_font_family );

		$all_saved_ff['venue_family'] = str_replace( ' ', '+', $ect_venue_font_famiily );
		$all_saved_ff['title_family'] = str_replace( ' ', '+', $ect_title_font_famiily );
		$all_saved_ff['desc_family']  = str_replace( ' ', '+', $ect_desc_font_famiily );
		$safe_fonts                   = array(
			'Arial',
			'Arial+Black',
			'Helvetica',
			'Times+New+Roman',
			'Courier+New',
			'Tahoma',
			'Verdana',
			'Impact',
			'Trebuchet+MS',
			'Comic+Sans+MS',
			'Lucida+Console',
			'Lucida+Sans+Unicode',
			'Georgia',
			'Palatino+Linotype',
		);
		$load_google_font             = ! empty( $options['ect_load_google_font'] ) ? $options['ect_load_google_font'] : 'yes';
		if ( $load_google_font == 'yes' ) {
			$build_url = esc_url( 'https://fonts.googleapis.com/css?family=' );
			$ff_names  = array();
			foreach ( $all_saved_ff as $key => $val ) {
				if ( ! in_array( $val, $safe_fonts ) ) {
					$ff_names[] = $val;
				}
			}
			if ( ! empty( $ff_names ) ) {
				$build_url .= implode( '|', array_filter( $ff_names ) );
				wp_enqueue_style( 'ect-google-font', "$build_url", array(), null, null, 'all' );
			}
		}

		if ( in_array( $template, array( 'timeline', 'classic-timeline', 'timeline-view' ) ) ) {
			require ECT_PLUGIN_DIR . '/templates/timeline/timeline-css.php';
		} elseif ( $template == 'minimal-list' ) {
			require ECT_PLUGIN_DIR . '/templates/minimal-list/minimal-list-css.php';
		} else {
			require ECT_PLUGIN_DIR . '/templates/list/list-css.php';
		}

		if ( ! empty( $custom_css ) ) {
			return $thisPlugin::minify_css( $ect_output_css . $custom_css );
		} else {
			return $thisPlugin::minify_css( $ect_output_css );
		}
	}

	public static function minify_css( $input ) {
		if ( trim( $input ) === '' ) {
			return $input;
		}
		return preg_replace(
			array(
				// Remove comment(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
				// Remove unused white-space(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
				// Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
				'#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
				// Replace `:0 0 0 0` with `:0`
				'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
				// Replace `background-position:0` with `background-position:0 0`
				'#(background-position):0(?=[;\}])#si',
				// Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
				'#(?<=[\s:,\-])0+\.(\d+)#s',
				// Minify string value
				'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
				'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
				// Minify HEX color code
				'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
				// Replace `(border|outline):none` with `(border|outline):0`
				'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
				// Remove empty selector(s)
				'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
			),
			array(
				'$1',
				'$1$2$3$4$5$6$7',
				'$1',
				':0',
				'$1:0 0',
				'.$1',
				'$1$3',
				'$1$2$4$5',
				'$1$2$3',
				'$1:0',
				'$1$2',
			),
			$input
		);
	}

}
