jQuery(window).on ('load', function () {
  setTimeout (function() {
//    var google_auto_placed = jQuery ('.google-auto-placed ins > ins > iframe[onload*="google"]:visible');
//    google_auto_placed.closest ('div').before ('<section class=\"ai-debug-bar ai-debug-adsense ai-adsense-auto-ads\">' + ai_front.automatically_placed + '</section>');
    var google_auto_placed = jQuery ('.google-auto-placed > ins');
    google_auto_placed.before ('<section class=\"ai-debug-bar ai-debug-adsense ai-adsense-auto-ads\">' + ai_front.automatically_placed + '</section>');
  }, 150);
});
