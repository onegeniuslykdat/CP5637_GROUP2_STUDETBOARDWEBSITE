<?php
/**
 * This file is used to  create settings in admin side
 *
 * @package the-events-calendar-single-event-templates-addon-free/includes
 */
namespace eptssettings;

if ( ! function_exists( 'tribe_get_events' ) ) {
	return;
}
function epta_get_all_temp() {
	$args     = array(
		'post_status' => 'publish,private',
		'post_type'   => 'epta',
	);
	$my_pages = get_pages( $args );
	// var_dump($my_pages);
	$get_temp_arr = array();
	foreach ( $my_pages as $my_page ) {
		$temp_key                  = $my_page->post_title;
		$get_temp_arr[ $temp_key ] = $my_page->post_title;

	}
	$temp_title = $get_temp_arr;
	return $temp_title;
	// var_dump($title);
}
$epta_get_all_temp = epta_get_all_temp();

/**
 * Get all categoery
 */
function epta_get_all_categoery() {
	$ect_cat_name = '';
	$ect_get_cat  = get_terms( 'tribe_events_cat' );
	if ( $ect_get_cat != '' ) :
		$ect_tslugs_arr = array();
		foreach ( $ect_get_cat as $ect_slug_name ) {
			$tecset_cat_slug                    = $ect_slug_name->slug;
			$ect_tslugs_arr[ $tecset_cat_slug ] = $ect_slug_name->slug;

		}
		$ect_cat_name = $ect_tslugs_arr;
	   endif;
	return $ect_cat_name;
}
$tecset_cate = epta_get_all_categoery();
/**
 * Get tag list
 */
function epta_get_tag() {
	$tecset_tag_name = '';
	$tecset_get_tag  = get_terms( 'post_tag' );

	  $tecset_tslugs_arr = array();
	foreach ( $tecset_get_tag as $tecset_tag_name ) {
		$tecset_get_tag                       = $tecset_tag_name->slug;
		$tecset_tslugs_arr[ $tecset_get_tag ] = $tecset_tag_name->slug;
	}
		$tecset_tag_name = $tecset_tslugs_arr;
		return $tecset_tag_name;
}
$tecset_admin_url   = admin_url( 'edit.php?page=tec-events-settings&tab=display&post_type=tribe_events#tec-settings-events-settings-display-date' );
$tecset_date_format = array(
	'default' => 'default',
	'DM'      => 'dM (01 Jan)',
	'MD'      => 'MD(Jan 01)',
	'FD'      => 'FD(January 01)',
	'DF'      => 'DF(01 January)',
	'FD,Y'    => 'FD,Y(January 01, 2019)',
	'MD,Y'    => 'MD,Y(Jan 01, 2019)',
	'MD,YT'   => 'MD,YT(Jan 01, 2019 8:00am-5:00pm)',
	'full'    => 'full(01 January 2019 8:00am-5:00pm)',
	'dFY'     => 'dFY(01 January 2019)',
	'dMY'     => 'dMY(01 Jan 2019)',
);
 $tecset_tag        = epta_get_tag();
/**
 * Get all events
 */
function epta_get_all_event() {
	$tecset_events = '';

	$events = tribe_get_events(
		array(
			'posts_per_page' => -1,

		)
	);

	$tecset_tevents_arr = array();
	foreach ( $events as $event ) {
		$tecset_all_event                        = $event->post_name;
		$tecset_tevents_arr[ $tecset_all_event ] = $event->post_title;
	}
	$tecset_events = $tecset_tevents_arr;
	return $tecset_events;
}
 $tecset_get_event_list = epta_get_all_event();

/**
 * Initiate Main Meta box
 */
$cmb = new_cmb2_box(
	array(
		'id'           => 'epta-generate-shortcode',
		'title'        => __( 'Main Settings', 'cmb2' ),
		'object_types' => array( 'epta' ), // Post type
		'context'      => 'normal',
		'priority'     => 'high',
		'show_names'   => true, // Show field names on the left
	)
);
	   /*
		 $cmb->add_field( array(
		  'name'    => 'Select Template',
		  'id'      => 'epta-select-temp',
		  'type'    => 'select',
		  // 'options' => $epta_get_all_temp,
		  'options' => array(
			'template-1' =>__('Template 1','cmb2'),
			'template-2' => __('Template 2', 'cmb2'),

		  ),

		  ) ); */
	$cmb->add_field(
		array(
			'name'    => 'Display On',
			'desc'    => 'Select a display condition where you want to apply this template.',
			'id'      => 'epta-apply-on',
			'type'    => 'select',
			'options' => array(
				'none'           => __( 'None', 'cmb2' ),
				'all-event'      => __( 'All Events', 'cmb2' ),
				'specific-event' => __( 'Specific Event', 'cmb2' ),
				'specific-cate'  => __( 'Specific Category', 'cmb2' ),
				'specific-tag'   => __( 'Specific Tag', 'cmb2' ),
			),
		)
	);

		$cmb->add_field(
			array(
				'name'       => 'Select Event',
				'id'         => 'epta-specific-event',
				'type'       => 'pw_multiselect',
				'options'    => $tecset_get_event_list,
				'attributes' => array(
					'required'               => true,
					'data-conditional-id'    => 'epta-apply-on',
					'data-conditional-value' => 'specific-event',
				),
			)
		);
		$cmb->add_field(
			array(
				'name'       => 'Select Category',
				'id'         => 'epta-categoery',
				'type'       => 'pw_multiselect',
				'options'    => $tecset_cate,
				'attributes' => array(
					'required'               => true,
					'data-conditional-id'    => 'epta-apply-on',
					'data-conditional-value' => 'specific-cate',
				),
			)
		);
		$cmb->add_field(
			array(
				'name'       => 'Select Tag</span>',
				'id'         => 'epta-tag',
				'type'       => 'pw_multiselect',
				'options'    => $tecset_tag,
				'attributes' => array(
					'required'               => true,
					'data-conditional-id'    => 'epta-apply-on',
					'data-conditional-value' => 'specific-tag',
				),
			)
		);

		$cmb->add_field(
			array(
				'name'    => 'Select Template',
				'desc'    => '<a target="_blank"
				href="https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings">
				Get Pro ⇗</a> for more templates and also edit a template with Elementor.',
				'id'      => 'epta-template',
				'type'    => 'select',
				'options' => array(
					'template-1' =>__('Template 1','cmb2'),
					'template-2' =>__('Template 2 (Pro Only)','cmb2'),
					'template-3' =>__('Template 3 (Pro Only)','cmb2'),
					'template-4' =>__('Edit with Elementor (Pro Only)','cmb2'),
				  ),
			)
		);
		$cmb->add_field(
			array(
				'name'    => 'Date Format',
				'desc'    => 'Select Date Format(Please check TEC settings for Default date format - <a href = "' . $tecset_admin_url . '">Click here </a>).',
				'id'      => 'tecset-date-format',
				'options' => $tecset_date_format,
				'type'    => 'select',
			)
		);

		$cmb->add_field(
			array(
				'name' => 'All Events Page Slug',
				'desc' => 'Enter your all events page slug, like - /events/',
				'id'   => 'epta-url',
				'type' => 'text',
			)
		);

		$cmb->add_field(
			array(
				'name' => 'Primary Color',
				'desc' => 'For heading background color.',
				'id'   => $prefix . 'primary-color',
				'type' => 'colorpicker',

			)
		);
			$cmb->add_field(
				array(
					'name' => 'Primary Alternate Color ',
					'desc' => 'For text where primary color will background color.',
					'id'   => $prefix . 'alternate-primary-color',
					'type' => 'colorpicker',

				)
			);
				$cmb->add_field(
					array(
						'name' => 'Secondary Color',
						'desc' => 'For sidebar background color.',
						'id'   => $prefix . 'secondary-alternate-color',
						'type' => 'colorpicker',

					)
				);
				$cmb->add_field( 
					array(
						'name' => 'Custom CSS',
						'desc' => 'Enter Custom CSS.',
						'id'   => 'epta-custom-css',
						'type' => 'textarea_code',
						'attributes' => array(
							'data-codeeditor' => json_encode( array(
								'codemirror' => array(
									'mode' => 'css',
								),
							) ),
						),
				) );



				
				/**
				 * Initiate Main Meta box
				 */
				$cmbforPro = new_cmb2_box(
					array(
						'id'           => 'epta-get-pro',
						'title'        => __( 'Get Pro Version', 'cmb2' ),
						'object_types' => array( 'epta' ), // Post type
						'context'      => 'side',
						'priority'     => 'low',
						'show_names'   => true, // Show field names on the left
					)
				);

				$cmbforPro->add_field(
					array(
						'name' => '',
						'desc' => '
    <b>Event Single Page Builder Pro</b><br/>
    <hr>
    ✅ More pre-desgined templates.<br/>
	✅ Build Single Page templates in Elementor.
   
	<hr>
    <a class="like_it_btn button button-primary" target="_blank"
    href="https://eventscalendaraddons.com/demos/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=demo&utm_content=sidebar">
    View Demo</a>
	<a class="like_it_btn button button-secondary" target="_blank"
    href="https://eventscalendaraddons.com/plugin/event-single-page-builder-pro/?utm_source=epta_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=sidebar">
    Get Pro ⇗</a>
    ',
						'type' => 'title',
						'id'   => 'wiki_test_title',
					)
				);

				/**
				 * Initiate Main Meta box
				 */
				$cmbforReview = new_cmb2_box(
					array(
						'id'           => 'epta-share-review',
						'title'        => __( 'Is it Helpful for you?', 'cmb2' ),
						'object_types' => array( 'epta' ), // Post type
						'context'      => 'side',
						'priority'     => 'low',
						'show_names'   => true, // Show field names on the left
					)
				);
				$cmbforReview->add_field(
					array(
						'name' => '',
						'desc' => '
						<a href="https://wordpress.org/support/plugin/event-page-templates-addon-for-the-events-calendar/reviews/#new-post" target="_blank">⭐⭐⭐⭐⭐</a><br/>
    If you like this plugin! Please give us a quick rating, it works as a boost for us to keep working on it.<br/><br/>
	A plugin by Coolplugins.net team.<br/><br/>

    <a href="https://wordpress.org/support/plugin/event-page-templates-addon-for-the-events-calendar/reviews/#new-post" class="like_it_btn button button-primary" target="_new" title="Submit Review">Submit Review</a>
    
   
    ',
						'type' => 'title',
						'id'   => 'wiki_test_title',
					)
				);


