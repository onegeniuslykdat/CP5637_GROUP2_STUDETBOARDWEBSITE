<?php
/**
 *
 * This file is responsible for creating all admin settings in Timeline Builder (post)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Can not load script outside of WordPress Enviornment!' );
}

if ( ! class_exists( 'ECTSettings' ) ) {
	class ECTSettings {


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
		 * The Constructor
		 */
		public function __construct() {
			// register actions
			$this->create_settings_panel();

		}


		public function create_settings_panel() {
			//
			// Metabox of the PAGE
			// Set a unique slug-like ID
			//
			$prefix_page_opts = 'ects';

			//
			// Create a metabox
			//
			// Set a unique slug-like ID
			$prefix = 'ects_options';
			if ( class_exists( 'CSF' ) ) {
				// Create options
				CSF::createOptions(
					$prefix,
					array(
						'framework_title'    => 'Events Shortcodes For The Events Calendar Settings',
						'menu_title'         => 'Shortcodes Settings',
						'menu_slug'          => 'tribe_events-events-template-settings',
						'menu_type'          => 'submenu',
						'menu_parent'        => 'cool-plugins-events-addon',
						'menu_icon'          => ECT_PLUGIN_URL . 'assets/css/ect-icons.svg',
						'nav'                => 'inline',
						'show_reset_section' => false,
						'show_sub_menu'      => false,
						'show_bar_menu'      => false,
					)
				);

				//
				// Create a section
				CSF::createSection(
					$prefix,
					array(
						'title'  => 'General Settings',

						'fields' => array(

							array(
								'title'   => 'Main Skin Color',
								'id'      => 'main_skin_color',
								'type'    => 'color',
								'desc'    => 'It is a main color scheme for all designs',
								'default' => '#5bbd8a',

							),
							array(
								'title'   => 'Main Skin Alternate Color / Font Color',
								'id'      => 'main_skin_alternate_color',
								'type'    => 'color',
								'desc'    => 'Text/Font color where background color is Main Skin.',
								'default' => '#ffffff',
							),
							array(
								'title'   => 'Featured Event Skin Color',
								'id'      => 'featured_event_skin_color',
								'type'    => 'color',
								'desc'    => 'This skin color applies on featured events',
								'default' => '#008cff',

							),
							array(
								'title'   => 'Featured Event Font Color',
								'id'      => 'featured_event_font_color',
								'type'    => 'color',
								'desc'    => 'This color applies on some fonts of featured events',
								'default' => '#ffffff',
							),
							array(
								'title'   => 'Event Background Color',
								'id'      => 'event_desc_bg_color',
								'type'    => 'color',
								'desc'    => 'This skin color applies on background of event description area.',
								'default' => '#ffffff',
							),
							array(
								'title'            => 'Event Title Styles',
								'id'               => 'ect_title_styles',
								'type'             => 'typography',
								'font_weight'      => 'bold',
								'font_style'       => 'normal',
								'desc'             => 'Select a style',
								'default'          => array(
									'color'            => '#383838',
									'font-family'      => 'Monda',
									'font-size'        => '18',
									'line-height'      => '1.5',
									'font-weight'      => '700',
									// 'font-style'=>'normal',
									'line_height_unit' => 'em',
								),
								'line_height_unit' => 'em',
							),
							array(
								'title'            => 'Events Description Styles',
								'id'               => 'ect_desc_styles',
								'type'             => 'typography',
								'desc'             => 'Select Styles',
								'default'          => array(
									'color'       => '#a5a5a5',
									'font-family' => 'Open Sans',
									'font-size'   => '15',
									'line-height' => '1.5',
								),
								'line_height_unit' => 'em',
							),
							array(
								'title'            => 'Event Venue Styles',
								'id'               => 'ect_desc_venue',
								'type'             => 'typography',
								'desc'             => 'Select a style',
								'default'          => array(
									'color'       => '#a5a5a5',
									'font-family' => 'Open Sans',
									'font-size'   => '15',
									'font-style'  => 'italic',
									'line-height' => '1.5',
								),
								'line_height_unit' => 'em',
							),

							array(
								'title'            => 'Event Dates Styles',
								'id'               => 'ect_dates_styles',
								'type'             => 'typography',
								'desc'             => 'Select a style',

								'default'          => array(
									'color'       => '#ffffff',
									'font-family' => 'Monda',

									'font-size'   => '36',
									'font-weight' => '700',

									'line-height' => '1',
								),
								'line_height_unit' => 'em',
							),
						),
					)
				);

				//
				// Create a section

				CSF::createSection(
					$prefix,
					array(
						'title'  => 'Extra Settings',
						'fields' => array(

							// A textarea field
							array(
								'title' => 'Custom CSS',
								'id'    => 'custom_css',
								'type'  => 'code_editor',
								'desc'  => 'Put your custom CSS rules here',
								'mode'  => 'css',
							),
							array(
								'title'   => 'No Event Text (Message to show if no event will available)',
								'id'      => 'events_not_found',
								'default' => 'There are no upcoming events at this time',
								'type'    => 'text',
								'desc'    => '',
							),
							array(
								'title'   => 'Update Find Out More label',
								'id'      => 'events_more_info',
								'default' => esc_html__( 'Find out more', 'ect' ),
								'type'    => 'text',
								'desc'    => '',
							),
							array(
								'id'    => 'ect_no_featured_img',
								'type'  => 'media',
								'title' => 'Default Image (select a default image, if no featured image for the event)',
							),
							array(
								'id'      => 'ect_display_categoery',
								'type'    => 'select',
								'title'   => 'Display category in templates',
								'desc'    => '<span style="color:red; font-size:14px;">Available in Pro plugin **</span>',
								'options' => array(
									'ect_enable_cat'  => 'Enable',
									'ect_disable_cat' => 'Disable',
								),
								'default' => 'ect_disable_cat',
							),
							array(
								'id'      => 'ect_load_google_font',
								'type'    => 'select',
								'title'   => 'Load Google Font',
								'options' => array(
									'yes' => 'Yes',
									'no'  => 'No',
								),
								'default' => 'yes',
							),

						),
					)
				);

				CSF::createSection(
					$prefix,
					array(
						'title'  => 'Shortcode Attributes',
						'fields' => array(

							array(
								'title'   => 'Default Shortcode',
								'type'    => 'heading',
								'content' => '<code>[events-calendar-templates category="all" template="default" style="style-1" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" socialshare="no" time="future"]</code>',
							),
							array(
								'type'     => 'callback',
								'function' => 'ect_shortcode_attr',
							// 'style' =>'solid ',
							),

						),
					)
				);

				function ect_demo_page_content() {

					ob_start();
					?>
					<div class="ect_started-section">
						<div class="ect_tab_btn_wrapper">
							<button class="button ect_class_post_button ect_tab_active">Events Shortcode</button>
							<button class="button ect_events_settings_button">Shortcode Settings</button>
							<button class="button button-info ect_events_shortcode_pro_button">Events Shortcode Pro</button>
						</div>
						<div class="tab_panel">
							<div class="ect_wrapper_first">
								<div class="ect_step">
									<div class="ect_step-content">
										<div class="ect_steps-title">
											<h2>1. Events Shortcode in (Classic Editor)</h2>
										</div>
										<div class="ect_steps-list">
											<ol>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Create or edit a page.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Click the <b>Events Shortcodes</b> button.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">A shortcode generator box with different shortcode attribute settings will appear.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Click the <b>Insert Shortcode</b> button to add the shortcode.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Publish the page and then preview the page.</span>
												</li>
											</ol>
										</div>
									</div>
									<div class="ect_video-section">
									      <iframe class="ect_events-video" width="560" height="315" src="https://www.youtube.com/embed/zNjnMwaP_3A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									</div>
								</div>
					
								<div class="ect_step ect_col-rev">
									<div class="ect_step-content">
										<div class="ect_steps-title">
											<h2>2. Events Shortcode Block</h2>
										</div>
										<div class="ect_steps-list">
											<ol>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Create or edit a page.</span>
												</li>
												<li class="ect_step-data">
					
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Search for <b>Events Shortcodes</b> in the block search box.
													</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Click on <b>Events Shortcode block</b> to add the block to the page.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Once the block is added, you can select the settings under block settings.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Publish the page and then preview the page.</span>
												</li>
					
											</ol>
										</div>
									</div>
									<div class="ect_video-section">
									<iframe class="ect_events-video" width="560" height="315" src="https://www.youtube.com/embed/kOh2tZGJREA?si=MwZsTcmj73JhzhZU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									</div>
								</div>
				
								<div class="ect_step">
									<div class="ect_step-content">
										<div class="ect_steps-title">
											<h2>3. Events Shortcode in (WPBakery Page Builder)</h2>
										</div>
										<div class="ect_steps-list">
											<ol>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Create or edit a page.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Click the <b>WPBakery Page Builder</b> button. Add the title and then go to backend editor. Select blank layout, add an element and click on the events calendar shortcode tab to get a shortcode generator.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">A shortcode generator box with different shortcode attribute settings will appear.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Click the <b>Save Changes</b> button to add the shortcode.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Publish the page and then preview the page.</span>
												</li>
											</ol>
										</div>
									</div>
									<div class="ect_video-section">
									<iframe class="ect_events-video" width="560" height="315" src="https://www.youtube.com/embed/q29GUhll4cA?si=2sCT72bRI8nVOfXA3" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									</div>
								</div>

							</div>
				
							<div class="ect_wrapper_second" style="display:none;">
				
								<div class="ect_step">
									<div class="ect_step-content">
										<div class="ect_steps-title">
											<h2>1. General Settings (Events Shortcode)</h2>
										</div>
										<div class="ect_steps-list">
											<ol>
												<li class="ect_step-data">
					
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Configure the general settings of Events Shortcode plugin to customize the look for your events.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Set color settings to style your events.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Set typography settings to style your events title, description, venue and dates content fonts.</span>
												</li>
											</ol>
										</div>
									</div>
									<div class="ect_video-section">
										<iframe class="ect_events-video" width="560" height="315" src="https://www.youtube.com/embed/RonG0_p2Gok" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									</div>
								</div>
					
								<div class="ect_step ect_col-rev">	
									<div class="ect_step-content">
										<div class="ect_steps-title">
											<h2>2. Extra Settings (Events Shortcode)</h2>
										</div>
										<div class="ect_steps-list">
											<ol>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">The Extra Settings will help you extend the texual and styling designs of the event display.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">To style the design using CSS, Add the Custom CSS under Custom CSS setting.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Customize the text of event No Event Text, Find Out More label text.</span>
												</li>
												<li class="ect_step-data">
													<!-- <span class="ect_list-icon"><i class="fa fa-check" aria-hidden="true"></i></span> -->
													<span class="ect_list-text">Set the default image to show when event doesn't have one.</span>
												</li>
											</ol>
										</div>
									</div>
									<div class="ect_video-section">
										<iframe class="ect_events-video" width="560" height="315" src="https://www.youtube.com/embed/RonG0_p2Gok?start=148"title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write;encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									</div>
								</div>
							</div>

							<div class="ect_wrapper_third" style="display:none;">
							     <h1>Why should you upgrade to PRO?</h1>
								<div class="ect_get_pro_content"> 
										    <table><tr>
												<td style="border: 1px solid #8ad4f9;"><ul class="p_feature-list">
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-grid/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Grid Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-masonry/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Masonry Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-carousel/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Carousel Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-slider/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Slider Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-accordion/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Accordion Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-calendar/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Calendar Layout</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-by-organizer/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Events by Organizer</a> (<b>PRO</b>)</li>
												<li>✅ <a href="https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-by-venue/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=buy_pro_tab" target="_blank">Events by Venue</a> (<b>PRO</b>)</li>
												<li>✅ Events Category Filters Inside Masonry (<b>PRO</b>)</li>
												<li>✅ Show Only Featured Events (<b>PRO</b>)</li>
												<li>✅ Events Schema SEO Support (<b>PRO</b>)</li>
												<li>✅ Premium Design & Settings (<b>PRO</b>)</li>
												<li>✅ Quick Premium Support (<b>PRO</b>)</li>
												</ul></td>
												</tr>
										    </table>
								</div>
							</div>
						</div>
					</div>
				
					<script>
						const EventDetailButton = jQuery(".ect_class_post_button");
						const EventsSettingsButton = jQuery(".ect_events_settings_button");
						const EventsShortcodeProButton = jQuery(".ect_events_shortcode_pro_button");
						const firstWrapper = jQuery(".ect_wrapper_first");
						const secondWrapper = jQuery(".ect_wrapper_second");
						const thirdWrapper = jQuery(".ect_wrapper_third");
						EventDetailButton.on("click", (event) => {
							firstWrapper.css("display","block");
							secondWrapper.css("display","none");
							thirdWrapper.css("display","none");
							event.preventDefault();
							EventDetailButton.siblings().removeClass('ect_tab_active');
							EventDetailButton.addClass('ect_tab_active');
						});
						EventsSettingsButton.on("click", (event) => {
							firstWrapper.css("display","none");
							secondWrapper.css("display","block");
							thirdWrapper.css("display","none");
							event.preventDefault();
							EventsSettingsButton.siblings().removeClass('ect_tab_active');
							EventsSettingsButton.addClass('ect_tab_active');
						});
						EventsShortcodeProButton.on("click", (event) => {
							firstWrapper.css("display","none");
							secondWrapper.css("display","none");
							thirdWrapper.css("display","block");
							event.preventDefault();
							EventsShortcodeProButton.siblings().removeClass('ect_tab_active');
							EventsShortcodeProButton.addClass('ect_tab_active');
						});
					</script>
						
					<!-- return $data; -->
					<?php
					return ob_get_clean();
				}

						// Create a section
						CSF::createSection(
							$prefix,
							array(
								'title'  => 'Get Started',
								'fields' => array(
									array(
										'id'      => 'shortcode_display',
										'type'    => 'content',
										'content' => ect_demo_page_content(),
									),
								),
							)
						);
			}
		}




	}

}

function ect_shortcode_attr() {
	   echo '
      <style>
        table.ect-shortcodes-tbl {
          width: 70%:;
          margin: auto;
          width: 50%;
        }   
        table.ect-shortcodes-tbl tr td{
          padding:15px;
        }
      </style>

      <h3>Shortcode Attributes</h3>
      <table class="ect-shortcodes-tbl" style="border:1px solid #ddd;">
      <tr style="border:1px solid #ddd"><th style="border:1px solid #ddd">Attribute</th><th style="border:1px solid #ddd">Value</th></tr>
      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">template</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>default</strong></li>
      <li><strong>timeline-view</strong></li>
      <li><strong>minimal-list</strong></li>
      <li><strong>grid-view</strong> (<a href="' . esc_attr( 'https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-grid/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=shortcode_attributes' ) . '" target="_blank">Pro Version</a>)</li>
      <li><strong>carousel-view</strong> (<a href="' . esc_attr( 'https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-carousel/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=shortcode_attributes' ) . '" target="_blank">Pro Version</a>)</li>
      <li><strong>slider-view</strong> (<a href="' . esc_attr( 'https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-slider/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=shortcode_attributes' ) . '" target="_blank">Pro Version</a>)</li>
      <li><strong>accordion-view</strong> (<a href="' . esc_attr( 'https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-accordion/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=shortcode_attributes' ) . '" target="_blank">Pro Version</a>)</li>
	  <li><strong>masonry-view</strong> (<a href="' . esc_attr( 'https://eventscalendaraddons.com/demos/events-shortcodes-pro/events-masonry/?utm_source=ect_plugin&utm_medium=inside&utm_campaign=demo&utm_content=shortcode_attributes' ) . '" target="_blank">Pro Version</a>)</li>
	  </ul></td></tr>

      <tr style="border:1px solid #ddd"><td  style="border:1px solid #ddd">style</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>style-1</strong></li>
      <li><strong>style-2</strong></li>
      <li><strong>style-3</strong></li>
      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">category</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>all</strong></li>
      <li><strong>custom-slug</strong> (events category slug)</li>
      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">date_format</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>default</strong> (01 January 2025)</li>
      <li><strong>MD,Y</strong> (Jan 01, 2025)</li>
      <li><strong>FD,Y</strong> (January 01, 2025)</li>
      <li><strong>DM</strong> (01 Jan)</li>
      <li><strong>DML</strong> (01 Jan Monday)</li>
      <li><strong>DF</strong> (01 January)</li>
      <li><strong>MD</strong> (Jan 01)</li>
      <li><strong>FD</strong> (January 01)</li>
      <li><strong>MD,YT</strong> (Jan 01, 2025 8:00am-5:00pm)</li>
      <li><strong>full</strong> (01 January 2025 8:00am-5:00pm)</li>
	  <li><strong>jMl</strong> (1 Jan Monday)</li>
	  <li><strong>d.FY</strong> (01. January 2025)</li>
	  <li><strong>d.F</strong> (01. January )</li>
	  <li><strong>lDF</strong> (Monday 01 January)</li>
	  <li><strong>Mdl</strong> (Jan 01 Monday)</li>
	  <li><strong>d.Ml</strong> (01. Jan Monday)</li>
	  <li><strong>dFT</strong> (01 January 8:00am - 5:00pm)</li>

      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">start_date<br/>end_date</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>YY-MM-DD</strong> (show events in between a date interval)</li>
      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">limit</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>10</strong> (number of events to show)</li>
      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">order</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>ASC</strong></li>
      <li><strong>DESC</strong></li>
      </ul></td></tr>

      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">hide_venue</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>yes</strong></li>
      <li><strong>no</strong></li>
      </ul></td></tr>
      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">socialshare</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>yes</strong></li>
      <li><strong>no</strong></li>
      </ul></td></tr>
      <tr style="border:1px solid #ddd"><td style="border:1px solid #ddd">time</td>
      <td style="border:1px solid #ddd"><ul>
      <li><strong>future</strong> (show future events)</li>
      <li><strong>past</strong> (show past events)</li>
      <li><strong>all</strong> (show all events)</li>
      </ul></td></tr>

      </table>';
}
