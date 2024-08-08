jQuery(document).ready(function ($) {
  $('a.tecset-share-link').click(function(e){
     e.preventDefault();
    var ect_href = event.currentTarget.getAttribute('href')
    window.open(ect_href, "", "width=800,height=400");
  });
});