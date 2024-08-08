<?php
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
 * This file is used only for dynamic styles in minimal layouts.
 */
// Silence is golden.
switch($style)
{
    case "style-1":
        //Main Skin color styles in minimal layouts
        $ect_output_css .='
        .ect-list-posts.style-1.ect-simple-event .ect-event-date-tag{
            color: '.$main_skin_color.';
        }
        #ect-minimal-list-wrp .ect-list-posts.style-1.ect-simple-event{
            border: 1px solid '.$main_skin_color.';
        }
        ';
        //Featured Event Skin color styles in minimal layouts
        $ect_output_css .='.ect-list-posts.style-1.ect-featured-event .ect-event-date-tag{
            color: '.$featured_event_skin_color.';
        }
        #ect-minimal-list-wrp .ect-list-posts.style-1.ect-featured-event{
            border: 1px solid '.$featured_event_skin_color.';
        }
       ';
        //Title styles in minimal layouts
        $ect_output_css .='
        #ect-minimal-list-wrp .style-1 .ect-events-title a{
            '.$title_styles.'
        }';
        //Time styles in minimal layouts
        $ect_output_css .=' #ect-minimal-list-wrp .ect-list-posts.style-1 .ect-event-date-tag .ect-event-datetimes span,
        #ect-minimal-list-wrp .style-1 span.ect-minimal-list-time{
            font-family: '.$ect_date_font_family.';
            font-style:'.$ect_date_font_style.';
            line-height:'.$ect_date_line_height.';
        }

        #ect-minimal-list-wrp .style-1 .ect-event-datetime{
            color: '.tinycolor($ect_title_color)->lighten(10)->toString().';
        }
        ';
        break;

    case "style-2":
         //Main Skin color styles in minimal layouts
         $ect_output_css .='
         .ect-list-posts.style-2.ect-simple-event .ect-event-date{
             color: '.$main_skin_color.';
         }
         ';
         //Featured Event Skin color styles in minimal layouts
         $ect_output_css .='.ect-list-posts.style-2.ect-featured-event .ect-event-date{
             color: '.$featured_event_skin_color.';
         }
        ';

        //Title styles in minimal layouts
        $ect_output_css .='#ect-minimal-list-wrp .style-2 span.ect-event-title a{
            '.$title_styles.'
        }';
        //Venue  styles in minimal layouts
        $ect_output_css .='#ect-minimal-list-wrp .style-2 .minimal-list-venue span,
        #ect-minimal-list-wrp .style-2 span.ect-google a {
            '.$ect_venue_styles.'
        }';
        break;
    
    case "style-3":
       
        $ect_output_css .='#ect-minimal-list-wrp .ect-list-posts.style-3.ect-featured-event{
            border-left: 4px solid '.$featured_event_skin_color.';
        }';
        $ect_output_css .='#ect-minimal-list-wrp .ect-list-posts.style-3.ect-simple-event{
            border-left: 4px solid '.$main_skin_color.';
        }';
     
            $ect_output_css .='#ect-minimal-list-wrp .ect-list-posts.style-3.ect-featured-event .ect-left-wrapper{
                background: '.tinycolor($featured_event_skin_color)->lighten(20)->toString().';
            }';

            $ect_output_css .='#ect-minimal-list-wrp .ect-list-posts.style-3.ect-simple-event .ect-left-wrapper{
                background: '.tinycolor($main_skin_color)->lighten(17)->toString().';
            }';
            
        $ect_output_css .=' #ect-minimal-list-wrp .style-3 .ect-events-title a{
            '.$title_styles.'
        }';
   
        $ect_output_css .='
        #ect-minimal-list-wrp .style-3 .ect-minimal-list-time{
            font-family: '.$ect_date_font_family.';
            color: '.tinycolor($ect_title_color)->lighten(10)->toString().';
            font-style:'.$ect_date_font_style.';
            line-height:'.$ect_date_line_height.';
        }
        ';
        $ect_output_css .='.ect-list-posts.style-3 .ect-event-dates{
            font-family: '.$ect_date_font_family.';
            font-style:'.$ect_date_font_style.';
            line-height:'.$ect_date_line_height.';
           color: '.$ect_date_color.';
        }
       ';
        break;
}
