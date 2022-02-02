jQuery(document).ready(function($){
    $('#toplevel_page_wpdiscuz, #toplevel_page_wpdiscuz > a').removeClass('wp-not-current-submenu');
    $('#toplevel_page_wpdiscuz, #toplevel_page_wpdiscuz > a').addClass('wp-has-current-submenu');
    $('#wpd-form-menu-item').parents('li').addClass('current');
});


