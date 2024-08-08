<?php
if (!defined('ABSPATH')) {
    exit;
} 
function ect_gutenberg_scripts() {
	$blockPath = '/dist/block.js';
	$stylePath = '/dist/block.css';

	// Enqueue the bundled block JS file
	wp_enqueue_script(
		'ect-block-js',
		plugins_url( $blockPath, __FILE__ ),
		[ 'wp-i18n', 'wp-blocks', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ],
		filemtime( plugin_dir_path(__FILE__) . $blockPath )
	);
	wp_localize_script( 'ect-block-js', 'ectUrl',array(ECT_PLUGIN_URL));

	// Enqueue frontend and editor block styles
	wp_enqueue_style(
		'ect-block-css',
		plugins_url( $stylePath, __FILE__ ),
		'',
		filemtime( plugin_dir_path(__FILE__) . $stylePath )
	);
	
}

// Hook scripts function into block editor hook
add_action( 'enqueue_block_editor_assets', 'ect_gutenberg_scripts' );

/**
 * Block Initializer
 * */
add_action( 'plugins_loaded', function () {
	if ( function_exists( 'register_block_type' ) ) {
		// Hook server side rendering into render callback

		register_block_type(
			'ect/shortcode', array(
				'render_callback' => 'ect_block_callback',
				'attributes' => array(
					'category' => array(
						'type' => 'string',
						'default' =>'all'
					),
					'template'	 => array(
						'type' => 'string',
						'default' =>'default'
					),
					'style'	 => array(
						'type' => 'string',
						'default' =>'style-1'
					),
					'dateformat'	=> array(
						'type'	=> 'string',
						'default' => 'default'
					),
					'limit'	=> array(
						'type'	=> 'string',
						'default' => '10'
					),				
					'order'	 => array(
						'type' => 'string',
						'default' =>'ASC'
					),
					'hideVenue'	=> array(
						'type'	=> 'string',
						'default' =>'no'
					),
					'time'	 => array(
						'type' => 'string',
						'default' =>'future'
					),					
					'startDate'	=> array(
						'type'	=> 'string',
						'default' => ''
					),
					'endDate'	=> array(
						'type'	=> 'string',
						'default' => ''
					),
					'socialshare'=> array(
						'type'	=> 'string',
						'default' =>'no',
					)		
				),
			)
		);
	}
} );

/**
 * Block Output
 * */
function ect_block_callback( $attr ) {
	extract( $attr );
	
	if ( isset( $template ) ) {
		$shortcode_string = '[events-calendar-templates
		category="%s"
		template="%s"
		style="%s" 
		date_format="%s"
		limit="%s"
		order="%s"
		hide-venue="%s"
		time="%s"
		start_date="%s"
		end_date="%s"
		socialshare="%s"]';
		$shortcode = sprintf($shortcode_string, $category, $template, $style, $dateformat, $limit,
		$order, $hideVenue, $time, $startDate, $endDate,$socialshare);
		
		return $shortcode;
	}
}
