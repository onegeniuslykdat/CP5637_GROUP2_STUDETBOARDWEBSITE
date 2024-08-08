<?php
/**
 *
 * This file is responsible for creating all admin settings in Timeline Builder (post)
 */
if (!defined("ABSPATH")) {
    exit('Can not load script outside of WordPress Enviornment!');
}

if (!class_exists('ECT_event_shortcode')) {
    class ECT_event_shortcode
    {

/**
 * The unique instance of the plugin.
 *
 */
        private static $instance;

        /**
         * Gets an instance of our plugin.
         *
         */
        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * The Constructor
         */
        public function __construct()
        {
            // register actions
            $this->ect_event_shortcode();
            add_action('admin_print_styles', array($this, 'ect_custom_shortcode_style'));

        }

        public function ect_custom_shortcode_style()
        {
            echo '<style>span.dashicon.dashicons.dashicons-ect-custom-icon:before {
        content:"";
        background: url(' . ECT_PLUGIN_URL . 'assets/images/ect-icons.svg);
        background-size: contain;
        background-repeat: no-repeat;
        height: 20px;
        display: block;
        }

         #wp-content-wrap a[data-modal-id="ect_shortcode_generator"]:before {
        content: "";
        background: url(' . ECT_PLUGIN_URL . 'assets/images/ect-icons.svg);
        background-size: contain;
        background-repeat: no-repeat;
        height: 17px;
        display: inline-block;
        margin: 0px 1px -3px 0;
        width: 20px;
        }

        #wp-content-wrap a[data-modal-id="ect_shortcode_generator"] {
      //  background: #000;
        //border-color: #000;
        }
        .csf-shortcode-single .csf-modal-content {
            height: 655px !important;

        }
          @media screen and (max-height: 700px) {
             #csf-modal-ect_shortcode_generator .csf-modal-inner {
                height: 95vh !important;
                overflow: auto;          
            }
            #csf-modal-ect_shortcode_generator .csf-modal-content {            
                overflow: hidden !important; 
                height:initial !important;        
                min-height: -webkit-fill-available;
            }  
        }

        </style>';
        }

        public function ect_event_shortcode()
        {
            $id = isset($GLOBALS['_GET']['post'])?absint($GLOBALS['_GET']['post']):'';
            $post_type = isset($GLOBALS['_GET']['post_type'][0])?wp_kses_post($GLOBALS['_GET']['post_type'][0]):get_post_type($id);
            if($post_type!=='page' && $post_type!=='post' && $post_type!='') { 
                return;
            }
            if (class_exists('CSF')) {

                //
                // Set a unique slug-like ID
                $prefix = 'ect_shortcode_generator';

                //
                // Create a shortcoder
                CSF::createShortcoder($prefix, array(
                    'button_title' => 'Events Shortcodes',
                    'insert_title' => 'Insert shortcode',
                    'gutenberg' => array(
                        'title' => 'Events Shortcodes',
                        'icon' => 'ect-custom-icon',
                        'description' => 'A shortcode generator for Events Calendar',
                        'category' => 'widgets',
                        'keywords' => array('shortcode', 'ect', 'event', 'code'),
                    ),
                ));

                //
                // A basic shortcode

                CSF::createSection($prefix, array(
                    'title' => 'Events Template',
                    'view' => 'normal', // View model of the shortcode. `normal` `contents` `group` `repeater`
                    'shortcode' => 'events-calendar-templates', // Set a unique slug-like name of shortcode.
                    'fields' => array(

                        array(
                            'id' => 'category',
                            'type' => 'select',
                            'title' => 'Events Category',
                            'placeholder' => 'Select Category',
                            'default' => 'all',
                            'chosen' => true,
                            'multiple' => true,
                            'desc'=>"Don't select alternate category if already you have selecetd all categories",
                            'settings' => array(
                                'width' => '50%',
                            ),
                            'options' => 'ect_free_select_category',
                        ),
                        array(
                            'id' => 'template',
                            'type' => 'select',
                            'title' => 'Select Template',
                            'default' => 'default',
                            'options' => array(
                                'default' => 'Default List Layout',
                                'timeline-view' => 'Timeline Layout',
                                'minimal-list' => 'Minimal List',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'style',
                            'type' => 'select',
                            'title' => 'Template Style',
                            'default' => 'style-1',
                            'options' => array(
                                'style-1' => 'Style 1',
                                'style-2' => 'Style 2',
                                'style-3' => 'Style 3',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'date_format',
                            'type' => 'select',
                            'title' => 'Date Formats',
                            'default' => 'default',
                            'options' => array(
                                'default' => 'Default (01 January 2019)',
                                'MD,Y' => 'Md,Y (Jan 01, 2019)',
                                'FD,Y' => 'Fd,Y (January 01, 2019)',
                                'DM' => 'dM (01 Jan)',
                                'DML' => 'dML (01 Jan Monday)',
                                'DF' => 'dF (01 January)',
                                'MD' => 'Md (Jan 01)',
                                'FD' => 'Fd (January 01)',
                                'MD,YT' => 'Md,YT (Jan 01, 2019 8:00am-5:00pm)',
                                'full' => 'Full (01 January 2019 8:00am-5:00pm)',
                                'jMl' => 'jMl (1 Jan Monday)',
                                'd.FY' => 'd.FY (01. January 2019)',
                                'd.F' => 'd.F (01. January)',
                                'ldF' => 'ldF (Monday 01 January)',
                                'Mdl' => 'Mdl (Jan 01 Monday)',
                                'd.Ml' => 'd.Ml (01. Jan Monday)',
                                'dFT' => 'dFT (01 January 8:00am-5:00pm)',

                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'limit',
                            'type' => 'text',
                            'title' => 'Limit the events',
                            'default' => '10',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),

                        ),
                        array(
                            'id' => 'order',
                            'type' => 'select',
                            'title' => 'Events Order',
                            'default' => 'ASC',
                            'options' => array(
                                'ASC' => 'ASC',
                                'DESC' => 'DESC',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'hide-venue',
                            'type' => 'select',
                            'title' => 'Hide Venue',
                            'default' => 'no',
                            'options' => array(
                                'yes' => 'YES',
                                'no' => 'NO',
                            ),
                            'dependency' => array(
                                'template', '!=', 'minimal-list',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'time',
                            'type' => 'select',
                            'title' => 'Events Time (Past/Future Events)',
                            'default' => 'future',
                            'options' => array(
                                'future' => 'Upcoming',
                                'past' => 'Past',
                                'all' => 'All',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'socialshare',
                            'type' => 'select',
                            'title' => 'Enable Social Share Buttons?',
                            'default' => 'no',
                            'options' => array(
                                'yes' => 'YES',
                                'no' => 'NO',
                            ),
                            'dependency' => array(
                                'template', '!=', 'minimal-list',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'ect-date-range-field',
                            'type' => 'date',
                            'title' => 'Show events between date range',
                            'custom_from_to' => true,
                            'settings' => array(
                                'dateFormat' => 'yy-mm-dd',
                                'changeMonth' => true,
                                'changeYear' => true,
                                "yearRange"=>"1800:2050",
                            ),
                            'attributes' => array(
                                'style' => 'width: 20%;',
                            ),

                        ),

                    ),
                ));
            }

            /**
             * Fetch all timeline items for shortcode builder options
             *
             * @return array $ids An array of timeline item's ID & title
             */
            if(!function_exists('ect_free_select_category')){
            function ect_free_select_category()
            {

                $terms = get_terms(array(
                    'taxonomy' => 'tribe_events_cat',
                    'hide_empty' => true,
                ));
                $ect_categories = array();
                $ect_categories['all'] = esc_html(__('All Categories', 'cool-timeline'));

                if (!empty($terms) || !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $ect_categories[$term->slug] = $term->name;
                    }
                }

                return $ect_categories;

            }
        }
    }

    }

}

new ECT_event_shortcode();
