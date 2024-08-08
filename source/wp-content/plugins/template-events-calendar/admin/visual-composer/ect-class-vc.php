<?php
if (!class_exists('EctVCAddon')) {

    class EctVCAddon
    {
        /**
         * The Constructor
         */
        public function __construct()
        {
            // We safely integrate with VC with this hook
            add_action( 'init', array($this, 'ect_vc_addon' ) );
        }

        function ect_vc_addon(){
           
                $terms = get_terms(array(
                    'taxonomy' => 'tribe_events_cat',
                    'hide_empty' => false,
                ));
                $ect_categories=array();
                $ect_categories['all'] = esc_html(__('all','ect2'));
        
                if (!empty($terms) || !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $ect_categories[$term->name] =$term->slug;
                    }
                }
               $date_formats= array(
                   
                esc_html(__( 'Default (01 January 2019)', 'ect2' ))=>'default',
                esc_html(__( 'Md,Y (Jan 01, 2019)', 'ect2' ))=>'MD,Y',
                esc_html(__( 'Fd,Y (January 01, 2019)', 'ect2' ))=>'FD,Y',
                esc_html(__( 'dM (01 Jan))', 'ect2' ))=> 'DM',
                esc_html(__( 'dMl (01 Jan Monday)', 'ect2' ))=>'DML',
                esc_html(__( 'dF (01 January)', 'ect2' ))=>'DF',
                esc_html(__( 'Md (Jan 01)', 'ect2' ))=>'MD',
                esc_html(__( 'Md,YT (Jan 01, 2019 8:00am-5:00pm)', 'ect2') )=> 'MD,YT',
                esc_html(__( 'Full (01 January 2019 8:00am-5:00pm)', 'ect2') )=>'full',
                esc_html(__( 'jMl', 'ect2' ))=> 'jMl',
                esc_html(__( 'd.FY (01. January 2019)', 'ect2' ))=>'d.FY',
                esc_html(__( 'd.F (01. January)', 'ect2') )=>'d.F',
                esc_html(__( 'ldF (Monday 01 January)', 'ect2') )=>'ldF',
                esc_html(__( 'Mdl (Jan 01 Monday)', 'ect2' ))=>'Mdl',
                esc_html(__( 'd.Ml (01. Jan Monday)', 'ect2' ))=>'d.Ml',
                esc_html(__( 'dFT (01 January 8:00am-5:00pm)', 'ect2' ))=>  'dFT',
                 
                    );
                    $templates=  array(
                      esc_html(__( "Default List Layout",'ect2' )) => "default",
                      esc_html( __( "Timeline Layout",'ect2')) => "timeline-view",
                      esc_html( __(  'Minimal List','ect2')) => 'minimal-list',
                               
                            );
                            $styles=  array(
                              esc_html(__( "Style 1",'ect2' )) => "style-1",
                              esc_html(__( "Style 2",'ect2')) => "style-2",
                              esc_html(__( "Style 3",'ect2')) => "style-3",
                               
                            );

             
                vc_map(array(
                    "name" => esc_html(__("The Events Calendar Shortcode", 'ect2')),
                    "base" => "events-calendar-templates",
                    "class" => "",
                    "controls" => "full",
                     "icon" => plugins_url('../../assets/images/ect-icons.svg', __FILE__), // or css class name which you can reffer in your css file later. Example: "cool-timeline_my_class"
                    "category" => __('The Events Calendar Shortcode', 'ect2'),
                   "params" => array(
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html(__( "Select Events Category",'ect2')),
                            "param_name" => "category",
                            "value" =>$ect_categories,
                           

                            'save_always' => true,
                        ),
                    array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Select Templates",'ect2'),
                             "param_name" => "template",
                            "value" => $templates,
                           
                    
                             'save_always' => true,
                         ),
                        
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => esc_html(__( "Select Styles",'ect2')),
                             "param_name" => "style",
                            "value" => $styles,
                           
                 
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html(__( "Date Format",'ect2')),
                          
                            "param_name" => "date_format",
                            "value" =>$date_formats,
                           

                            'save_always' => true,
                        ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Events Order",'ect2'),
                             "param_name" => "order",
                             "value" => array(
                              esc_html(__( "ASC",'ect2' )) => "ASC",
                              esc_html(__( "DESC",'ect2')) => "DESC",
                                            
                                           ),
                            
                               'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => esc_html(__( "Hide Venue",'ect2')),
                             "param_name" => "hide-venue",
                             "value" => array(
                              esc_html(__( "no",'ect2' )) => "no",
                              esc_html( __( "Yes",'ect2')) => "yes",
                                            
                                           ),
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Enable Social Share Buttons",'ect2'),
                             "param_name" => "socialshare",
                             "value" => array(
                              esc_html(__( "no",'ect2' )) => "no",
                              esc_html(__( "Yes",'ect2')) => "yes",
                                            
                                           ),
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Show Events",'ect2'),
                             "param_name" => "time",
                             "value" => array(
                              esc_html(__( "Upcoming Events",'ect2' )) => "future",
                              esc_html(__( "Past Events",'ect2')) => "past",
                              esc_html(__( "All (Upcoming + Past)",'ect2')) => "all",
                                            
                                           ),
                            
                           
                   
                             'save_always' => true,
                         ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => esc_html(__( "Limit the events",'ect2')),
                             "param_name" => "limit",
                             "value" => '10',
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => esc_html(__( "Start Date | format(YY-MM-DD)",'ect2')),
                             "param_name" => "start_date",
                             "value" => '',
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => esc_html(__( "End Date | format(YY-MM-DD)",'ect2')),
                             "param_name" => "end_date",
                             "value" => '',
                           
                             'save_always' => true,
                         ),

                 

                    )
                ));



            }
        }// vc function end
    
}
new EctVCAddon();