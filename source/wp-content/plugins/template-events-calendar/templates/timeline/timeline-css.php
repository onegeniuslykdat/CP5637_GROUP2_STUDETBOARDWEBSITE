<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
 * This file is used only for dynamic styles in timeline layouts.
 */
switch ( $style ) {
	case 'style-1':
		$ect_output_css .= ' #event-timeline-wrapper .ect-timeline-post.style-1.ect-featured-event .ect-lslist-event-detail a:hover{
            background: ' . $featured_event_skin_color . ';
            color: ' . $featured_event_font_color . ';
        }';

		if ( $main_skin_alternate_color === '' ) {
			 $ect_output_css .= ' #event-timeline-wrapper .ect-timeline-post.style-1.ect-simple-event .ect-lslist-event-detail a:hover{
                 background: ' . $main_skin_color . ';
                 color: ' . $ect_date_color . ';
            }';
		} else {
			 $ect_output_css .= '#event-timeline-wrapper .ect-timeline-post.style-1.ect-simple-event .ect-lslist-event-detail a:hover{
                 background: ' . $main_skin_color . ';
                 color: ' . $main_skin_alternate_color . ';
            }';
		}

		break;
	case 'style-2':
		$ect_output_css .= ' #event-timeline-wrapper .ect-timeline-post.style-2.ect-featured-event .timeline-content .ect-lslist-event-detail a{
            background: ' . $featured_event_skin_color . ';
            color: ' . $featured_event_font_color . ';
        }';

		if ( $main_skin_alternate_color === '' ) {
			 $ect_output_css .= ' #event-timeline-wrapper .ect-timeline-post.style-2.ect-simple-event .timeline-content .ect-lslist-event-detail a{
                 background: ' . $main_skin_color . ';
                 color: ' . $ect_date_color . ';
            }';
		} else {
			 $ect_output_css .= '#event-timeline-wrapper .ect-timeline-post.style-2.ect-simple-event .timeline-content .ect-lslist-event-detail a{
                 background: ' . $main_skin_color . ';
                 color: ' . $main_skin_alternate_color . ';
            }';
		}

		break;
	case 'style-3':
		break;
	default:
		$ect_output_css .= '
    #event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-4 .timeline-content p{
        color:' . $featured_event_font_color . ';
    }
    #event-timeline-wrapper.style-4 h2.content-title
    {
        background: ' . $event_desc_bg_color . ';
    }
    #event-timeline-wrapper .ect-timeline-post.style-4 .timeline-content:before{
        border: 2px solid ' . $event_desc_bg_color . ';	
    }
    #event-timeline-wrapper .ect-timeline-post .timeline-dots-style-4{
        background-color:' . $event_desc_bg_color . ';
    }
    // #event-timeline-wrapper .ect-timeline-year-style-4 { 
    //     -webkit-box-shadow: 0 0 0 4px white, 0 0 0 8px ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ';
    //     box-shadow: 0 0 0 4px white, 0 0 0 8px ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ';
    // }
    #event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-4 .ect-date-area {
        ' . $ect_date_style . '
    }
    #event-timeline-wrapper .ect-timeline-year-style-4 .year-placeholder span{
    font-family: ' . $ect_date_font_family . ';
    color: ' . $ect_date_color . ';
    }
   ';
		break;
}
  // for event border
  $ect_output_css   .= ' #event-timeline-wrapper .ect-timeline-post.ect-featured-event .timeline-content{
    border: 1px solid;
    border-color: ' . $featured_event_skin_color . ';
    }';
	$ect_output_css .= ' #event-timeline-wrapper .ect-timeline-post.ect-simple-event .timeline-content{
        border: 1px solid;
        border-color: ' . $main_skin_color . ';
        }';
// border and outer-trainglesettign
$ect_output_css                .= '#event-timeline-wrapper .ect-timeline-post.ect-simple-event .ect-date-area.timeline-view-schedule{
    background: ' . $main_skin_color . ';
    color: ' . $main_skin_alternate_color . ';
}';
$ect_output_css                .= '#event-timeline-wrapper .ect-timeline-post.ect-featured-event .ect-date-area.timeline-view-schedule{
    background: ' . $featured_event_skin_color . ';
    color: ' . $featured_event_font_color . ';
}';
 $ect_output_css               .= '
    #event-timeline-wrapper .ect-timeline-post.odd.ect-featured-event .timeline-content.odd:before{
        border-left-color:  ' . $featured_event_skin_color . ';
    }
    #event-timeline-wrapper .ect-timeline-post.odd.ect-simple-event .timeline-content.odd:before{
        border-left-color:  ' . $main_skin_color . ';
    }
';
$ect_output_css                .= '
    #event-timeline-wrapper .ect-timeline-post.even.ect-featured-event .timeline-content.even:before{
        border-right-color:  ' . $featured_event_skin_color . ';
    }
    #event-timeline-wrapper .ect-timeline-post.even.ect-simple-event .timeline-content.even:before{
        border-right-color:  ' . $main_skin_color . ';
    }
';
	$ect_output_css            .= '
    #event-timeline-wrapper .ect-timeline-year {
                   background: ' . tinycolor( $main_skin_color )->darken( 10 )->toString() . ';
                   background: radial-gradient(circle farthest-side,  ' . tinycolor( $main_skin_color )->darken( 0 )->toString() . ',  ' . tinycolor( $main_skin_color )->darken( 10 )->toString() . ');
               }
               #event-timeline-wrapper .ect-timeline-post .timeline-dots {
                   background:  ' . tinycolor( $main_skin_color )->darken( 10 )->toString() . ';
               }';
			   $ect_output_css .= '#event-timeline-wrapper .cool-event-timeline .ect-timeline-year .year-placeholder span{
					color: ' . $main_skin_alternate_color . ';
				}';
			   // Timeline Feature Skin Color
	$ect_output_css .= '#event-timeline-wrapper .ect-timeline-post.ect-featured-event .timeline-dots {
                   background: ' . $featured_event_skin_color . ';
               }';
			   // Timeline bg Color
			   $ect_output_css .= '#event-timeline-wrapper .ect-timeline-post .timeline-content {
                   background: ' . $event_desc_bg_color . ';
               }
               #event-timeline-wrapper .cool-event-timeline:before {
                   background-color: ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ';
               }
               #event-timeline-wrapper .ect-timeline-year { 
                   -webkit-box-shadow: 0 0 0 4px white, 0 0 0 8px ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ';
                   box-shadow: 0 0 0 4px white, 0 0 0 8px ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ';
               }
               #event-timeline-wrapper:before,
               #event-timeline-wrapper:after {
                   background-color: ' . tinycolor( $event_desc_bg_color )->darken( 5 )->toString() . ' !important;
               }';
				   // Timeline Title styles
				   $ect_output_css .= '#event-timeline-wrapper .ect-timeline-post h2.content-title,
                   #event-timeline-wrapper .ect-timeline-post h2.content-title a.ect-event-url {
                       ' . $title_styles . '
                   }
                   #event-timeline-wrapper .ect-timeline-post h2.content-title a.ect-event-url:hover {
                       color: ' . tinycolor( $ect_title_color )->darken( 10 )->toString() . '; 
                   }
                   event-timeline-wrapper .cool-event-timeline .ect-timeline-post .timeline-content .content-details a{
                        color: ' . $ect_title_color . ';
                   }
               ';

			   /* Timeline Description Styles ( Timeline ) */
			   $ect_output_css .= '#event-timeline-wrapper .ect-timeline-post .timeline-content,
               #event-timeline-wrapper .ect-timeline-post .timeline-content p {
                    ' . $ect_desc_styles . '
               }';
			   // Timeline date style
			   $ect_output_css .= '
                   #event-timeline-wrapper .ect-timeline-post .ect-date-area {
                        ' . $ect_date_style . '
               }
               #event-timeline-wrapper .ect-timeline-post span.ect-custom-schedule{
                ' . $ect_date_style . '
               }
               ';
			   /* Timeline Venue Styles ( Timeline ) */
			   $ect_output_css .= '#event-timeline-wrapper .timeline-view-venue
                {
                   ' . $ect_venue_styles . '
              }    
              #event-timeline-wrapper .ect-rate-area .ect-rate {
                   font-size: ' . $ect_title_font_size . 'px;
                   font-family: ' . $ect_title_font_famiily . ';
              }
              #event-timeline-wrapper .ect-rate-area .ect-icon .ect-icon-ticket,
              #event-timeline-wrapper .ect-rate-area .ect-rate,
              #event-timeline-wrapper .ect-rate-area .ect-ticket-info span.tribe-tickets-left{
                   color: ' . $ect_title_color . ';
              }
              #event-timeline-wrapper .ect-timeline-post .ect-google a {
                   color: ' . $ect_venue_color . ';
              }
              #event-timeline-wrapper .ect-timeline-year .year-placeholder span,
                {
                font-family: ' . $ect_date_font_family . ';
                color: ' . $ect_date_color . ';
                }
                ';
$ect_output_css                .= '
#ect-timeline-wrapper .ect-simple-event ect-timeline-header .ect-date-area {
     background: ' . $thisPlugin::ect_hex2rgba( $main_skin_color, .95 ) . ';
     
}';
if ( $main_skin_alternate_color !== '' ) {
	 $ect_output_css .= '
     #ect-timeline-wrapper .ect-simple-event ect-timeline-header .ect-date-area {
          color: ' . $main_skin_alternate_color . ';
     }';
}
   /**------------------------------------Share css------------------------------ */
   $ect_output_css .= ' #event-timeline-wrapper .ect-featured-event .ect-share-wrapper i.ect-icon-share:before{
    background: ' . $featured_event_font_color . ';
    color: ' . $featured_event_skin_color . ';
}';

if ( $main_skin_alternate_color === '' ) {
	 $ect_output_css .= '  #event-timeline-wrapper .ect-simple-event .ect-share-wrapper i.ect-icon-share:before{
         background: ' . $ect_date_color . ';
         color: ' . $main_skin_color . ';
    }';
} else {
	 $ect_output_css .= ' #event-timeline-wrapper .ect-simple-event .ect-share-wrapper i.ect-icon-share:before{
         background: ' . $main_skin_alternate_color . ';
         color: ' . $main_skin_color . ';
    }';
}
$ect_output_css .= '   
#event-timeline-wrapper .ect-featured-event .ect-share-wrapper .ect-social-share-list a:hover{
     color: ' . $featured_event_skin_color . ';
}
#event-timeline-wrapper .ect-simple-event .ect-share-wrapper .ect-social-share-list a:hover{
   color: ' . $main_skin_color . ';
}
#ect-timeline-wrapper .ect-featured-event .ect-share-wrapper i.ect-icon-share:before{
    background: ' . $featured_event_font_color . ';
    color: ' . $featured_event_skin_color . ';
}
#ect-timeline-wrapper .ect-simple-event .ect-share-wrapper i.ect-icon-share:before{
    background: ' . $main_skin_alternate_color . ';
    color: ' . $main_skin_color . ';
}';
 /**------------------------------------Readmore css------------------------------ */
 $ect_output_css .= '   
#event-timeline-wrapper .ect-timeline-post.ect-simple-event .timeline-content .ect-lslist-event-detail a{
    border-color: ' . $main_skin_color . ';
    color: ' . $main_skin_color . ';
}
#event-timeline-wrapper .ect-timeline-post.ect-featured-event .timeline-content .ect-lslist-event-detail a{
    border-color: ' . $featured_event_skin_color . ';
    color: ' . $featured_event_skin_color . ';
}
';
$ect_output_css  .= '
#ect-timeline-wrapper .ect-date-area,
#ect-timeline-wrapper .ect-date-area span{
     ' . $ect_date_style . ';
}
';
if ( $ect_date_styles['font-size'] > '20' ) {
	$ect_output_css .= '
    #event-timeline-wrapper .ect-timeline-post .ect-date-area ,
    #event-timeline-wrapper .ect-timeline-post .ect-date-area span{
              font-size:20px;
         }
         ';
}
