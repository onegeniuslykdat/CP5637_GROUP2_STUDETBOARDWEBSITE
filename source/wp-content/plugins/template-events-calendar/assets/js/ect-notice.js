jQuery(document).ready(function ($) {
    var id = $('.ect-main-notice-wrp').attr('id');
    var slug = $('.ect-main-notice-wrp').data('slug');
   
 $(document).on("click", "#"+id + ' '+'.'+slug+'_dismiss_notice', function (event) {
     
      var mainwrp = '.ect-feedback-notice-wrapper';
      
      
        var ajaxURL=$(mainwrp).data("ajax-url");
       
        var ajaxCallback=$(mainwrp).data("ajax-callback");
        var slug = $(mainwrp).data("plugin-slug");
        var id = $(mainwrp).attr("id");
        var wp_nonce = $(mainwrp).data("wp-nonce");
      
        $.post(ajaxURL, { "action":'cool_plugins_admin_review_notice_dismiss',"slug":slug,"id":id,"_nonce":wp_nonce }, function( data ) {
            $(mainwrp).slideUp("fast");
          })
    });
});
