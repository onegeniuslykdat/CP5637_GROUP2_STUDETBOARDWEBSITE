<?php
if (!defined('ABSPATH')) {
   exit;
} 
/**
 * 
 * Addon dashboard sidebar.
 */

 if( !isset($this->main_menu_slug) ):
    return false;
 endif;

 $event_support = esc_url("https://eventscalendaraddons.com/support/?utm_source=events_dashboard&utm_medium=inside&utm_campaign=support&utm_content=sidebar");
 $pluginwebsite = esc_url("https://eventscalendaraddons.com/?utm_source=events_dashboard&utm_medium=inside&utm_campaign=homepage&utm_content=sidebar");
 $pro_plugins_visit_website = esc_url("https://eventscalendaraddons.com/plugins/?utm_source=events_dashboard&utm_medium=inside&utm_campaign=addons&utm_content=sidebar");
 $companywebsite = esc_url("https://coolplugins.net/?utm_source=events_dashboard&utm_medium=inside&utm_campaign=coolplugins&utm_content=sidebar");
 $tec_pro = esc_url("https://theeventscalendar.pxf.io/events-calendar-pro");
?>

<div class="cool-body-right">
<h4><a href="<?php echo esc_url($pluginwebsite);?>" target="_blank">EventsCalendarAddons.com ⇗</a>  </h4>
<ul>
      <li>Shortcodes to show events list in any page or post.</li>
      <li>Use <b>The Events Calendar</b> plugin in Elementor pages easily.</li>
      <li>Edit The Events Calendar event single page template easily. </li>
      <li>These addons have <b>30000+</b> active installs. </li>
      <li><a  href="<?php echo esc_url($companywebsite);?>" target="_blank">CoolPlugins.net</a> is the company behind these addons. </li>
      <li>For any query or support, please contact plugin support team. </li>
      </ul>    
      <br/>
      <a href="<?php echo esc_url($event_support); ?>" target="_blank" class="button button-primary">👉 Plugin Support</a>
      <br/><br/>
      <a href="<?php echo esc_url($pro_plugins_visit_website); ?>" target="_blank" class="button button-secondary">👉 Pro Addons</a>
      <br/><br/>
      <hr>
      <p> Our addons also work smoothly with <a  href="<?php echo esc_url($tec_pro);?>" target="_blank">Events Calendar Pro ⇗</a> <b>(official premium plugin by The Events Calendar)</b></p>
      <a href="<?php echo esc_url($tec_pro);?>" target="_blank"><img src="<?php echo esc_url(plugin_dir_url( $this->addon_file ) .'/assets/images/events-calendar-pro.png'); ?>"  width="200"></a>
      <br/><br/>
      <hr>
</div>

</div><!-- End of main container-->