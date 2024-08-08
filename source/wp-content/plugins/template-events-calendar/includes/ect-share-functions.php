<?php
/**
 * This file is used to share events.
 * 
 * @package the-events-calendar-templates-and-shortcode/includes
 */

function ect_share_button($event_id){
  $ect_sharecontent = '';
  $ect_geturl = esc_url(get_permalink($event_id));
  $ect_gettitle = sanitize_title(get_the_title($event_id));
  $subject= str_replace("+"," ",$ect_gettitle);
  // Construct sharing URL
    $ect_twitterURL = esc_url('https://twitter.com/intent/tweet?text='.$ect_gettitle.'&amp;url='.$ect_geturl.'');
    $ect_whatsappURL = esc_url('https://web.whatsapp.com/send/?text='.$ect_gettitle . ' ' . $ect_geturl);
    $ect_facebookurl = esc_url('https://www.facebook.com/sharer/sharer.php?u='.$ect_geturl.'');
    $ect_emailUrl = esc_url('mailto:?Subject='.$subject.'&Body='.$ect_geturl.'');
   $ect_linkedinUrl = esc_url("http://www.linkedin.com/shareArticle?mini=true&amp;url=$ect_geturl");
    // Add sharing button at the end of page/page content
    $ect_sharecontent .= '<div class="ect-share-wrapper">';
    $ect_sharecontent .= '<i class="ect-icon-share"></i>';
    $ect_sharecontent .= '<div class="ect-social-share-list">';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.esc_url($ect_facebookurl).'" target="_blank" title="Facebook" aria-haspopup="true"><i class="ect-icon-facebook"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.esc_url($ect_twitterURL).'" target="_blank" title="Twitter" aria-haspopup="true"><i class="ect-icon-twitter"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.esc_url($ect_linkedinUrl).'" target="_blank" title="Linkedin" aria-haspopup="true"><i class="ect-icon-linkedin"></i></a>';
    $ect_sharecontent .= '<a class="ect-email" href="'.esc_url($ect_emailUrl).'"><i class="ect-icon-mail"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.esc_url($ect_whatsappURL).'" target="_blank" title="WhatsApp" aria-haspopup="true"><i class="ect-icon-whatsapp"></i></a>';
    $ect_sharecontent .= '</div></div>';
    return $ect_sharecontent;
}
